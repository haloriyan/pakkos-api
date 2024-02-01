<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingFacility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public static $photoKeys = [
        'front_building_photo', 'inside_building_photo', 'streetview_building_photo',
        'front_room_photo', 'inside_room_photo', 'bath_room_photo', 'other_photo'
    ];

    public function getFacilities($listing) {
        $facilitiesRaw = ListingFacility::where('listing_id', $listing->id)->with(['facility'])->get();
        $facilities = [];
        $types = [];

        foreach ($facilitiesRaw as $fac) {
            $facilities[$fac->facility->type][] = $fac;
            if (!in_array($fac->facility->type, $types)) {
                array_push($types, $fac->facility->type);
            }
        }

        return $facilities;
    }
    public function get(Request $request) {
        $userID = $request->header("user_id");
        if ($userID != null) {
            $listings = Listing::where('user_id', $userID)->with(['facilities'])->get();
        } else {
            $listings = Listing::orderBy('created_at', 'DESC')
            ->with(['user', 'facilities'])
            ->get();
        }

        foreach ($listings as $l => $listing) {
            $listings[$l]->facilities_display = $this->getFacilities($listing);
        }

        return response()->json([
            'listings' => $listings,
        ]);
    }
    public function getByID($id) {
        $listing = Listing::where('id', $id)->with(['facilities'])->first();
        if ($listing == null) {
            $listing = Listing::where('slug', $id)->with(['facilities'])->first();
        }

        $listing->facilities_display = $this->getFacilities($listing);
        
        return response()->json([
            'listing' => $listing,
        ]);
    }
    public function create(Request $request) {
        $toCreate = [
            'user_id' => $request->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'slug' => $request->slug,
            'consumer_name' => $request->consumer_name,
            'price' => $request->price,
            'price_inclusion' => json_encode($request->price_inclusion),
            'province' => $request->province,
            'city' => $request->city,
            'subdistrict' => $request->subdistrict,
            'address' => $request->address,
            'address_note' => $request->address_note,
            'room_size' => $request->room_size,
            'room_total' => $request->room_total,
            'room_available' => $request->room_available,
            'is_approved' => null,
        ];

        foreach (self::$photoKeys as $photo) {
            if ($request->hasFile($photo)) {
                $ph = $request->file($photo);
                $phName = $ph->getClientOriginalName();
                $toCreate[$photo] = $phName;
                $ph->storeAs('public/listing_photos', $phName);
            }
        }

        $saveData = Listing::create($toCreate);

        // handling facilities
        foreach (explode(",", $request->facilities) as $facility) {
            $saveFacility = ListingFacility::create([
                'listing_id' => $saveData->id,
                'facility_id' => $facility,
            ]);
        }

        return response()->json([
            'message' => ""
        ]);
    }
    public function delete(Request $request) {
        $data = Listing::where('id', $request->listing_id);
        $listing = $data->first();

        $deleteData = $data->delete();
        foreach (self::$photoKeys as $key) {
            Storage::delete('public/listing_photos/' . $listing->{$key});
        }

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function update($id, Request $request) {
        $data = Listing::where('id', $id);
        $listing = $data->first();
        $toUpdate = [];
        $section = $request->section;

        if ($section == "basic") {
            $toUpdate = [
                'name' => $request->name,
                'consumer_name' => $request->consumer_name,
                'description' => $request->description,
                'slug' => $request->slug,
            ];
        }
        if ($section == "location") {
            $toUpdate = [
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'subdistrict' => $request->subdistrict,
            ];
        }
        if ($section == "facility") {
            $oldFacilities = [];
            $newFacilities = $request->facilities_id;
            
            foreach ($listing->facilities as $fac) {
                array_push($oldFacilities, $fac->id);
            }

            foreach (explode(",", $newFacilities) as $newFac) {
                if (!in_array($newFac, $oldFacilities)) {
                    ListingFacility::create([
                        'listing_id' => $listing->id,
                        'facility_id' => $newFac
                    ]);
                }
            }

            foreach ($oldFacilities as $oldFac) {
                if (!in_array($oldFac, explode(",", $newFacilities))) {
                    // remove
                    ListingFacility::where([
                        ['listing_id', $listing->id],
                        ['facility_id', $oldFac]
                    ])->delete();
                }
            }
        }
        if ($section == "price") {
            $toUpdate = [
                'price' => $request->price,
                'price_inclusion' => $request->price_inclusion,
            ];
        }
        if ($section == "building") {
            $photoKeys = [
                'front_building_photo', 'inside_building_photo', 'streetview_building_photo',
            ];
            foreach ($photoKeys as $photo) {
                if ($request->hasFile($photo)) {
                    $ph = $request->file($photo);
                    $phName = $ph->getClientOriginalName();
                    $toUpdate[$photo] = $phName;
                    $ph->storeAs('public/listing_photos', $phName);
                    $deleteOldPhoto = Storage::delete('public/listing_photos/' . $listing->{$photo});
                }
            }
        }
        if ($section == "quantity") {
            $toUpdate = [
                'room_total' => $request->room_total,
                'room_available' => $request->room_available,
                'room_size' => $request->room_size,
            ];
        }
        if ($section == "room") {
            $photoKeys = [
                'front_room_photo', 'inside_room_photo', 'bath_room_photo', 'other_photo'
            ];
            foreach ($photoKeys as $photo) {
                if ($request->hasFile($photo)) {
                    $ph = $request->file($photo);
                    $phName = $ph->getClientOriginalName();
                    $toUpdate[$photo] = $phName;
                    $ph->storeAs('public/listing_photos', $phName);
                    $deleteOldPhoto = Storage::delete('public/listing_photos/' . $listing->{$photo});
                }
            }
        }

        $updateData = $data->update($toUpdate);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function approval(Request $request) {
        $data = Listing::where('id', $request->listing_id);
        $data->update([
            'is_approved' => $request->action == "accept" ? true : false,
        ]);
        
        return response()->json([
            'message' => "ok"
        ]);
    }
}
