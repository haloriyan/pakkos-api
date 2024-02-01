<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function get() {
        $templatesRaw = Template::orderBy('type', 'DESC')->get();
        $templates = [];
        $types = ['Pertanyaan Pengguna', 'Aktivitas Pengguna'];

        foreach ($templatesRaw as $temp) {
            $templates[$temp->type][] = $temp;
            // if (!in_array($temp->type, $types)) {
            //     array_push($types, $temp->type);
            // }
        }

        return response()->json([
            'templates' => $templates,
            'types' => $types,
        ]);
    }
    public function create(Request $request) {
        $saveData = Template::create([
            'type' => $request->type,
            'body' => $request->body,
            'count' => 0,
        ]);

        return response()->json([
            'message' => "ok"
        ]);
    }
    public function delete(Request $request) {
        $data = Template::where('id', $request->template_id);
        $data->delete();

        return response()->json([
            'message' => "ok"
        ]);
    }
}
