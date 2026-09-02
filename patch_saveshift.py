import os

with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

# Using slicing to manually replace saveShift
start_str = "public function saveShift(Request $request)"
end_str = "return response()->json(['success' => true]);\n    }"

if start_str in content and end_str in content:
    start_idx = content.find(start_str)
    end_idx = content.find(end_str) + len(end_str)
    
    new_method = """public function saveShift(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'user_id' => [
                'required',
                \\Illuminate\\Validation\\Rule::exists('users', 'id')->where('shop_id', $user->shop_id)
            ],
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $data = [
            'user_id' => $request->user_id,
            'shop_id' => $user->shop_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
        ];

        if ($request->filled('id')) {
            $shift = CrewShift::where('shop_id', $user->shop_id)->findOrFail($request->id);
            $shift->update($data);
        } else {
            CrewShift::create($data);
        }

        return response()->json(['success' => true]);
    }"""
    
    content = content[:start_idx] + new_method + content[end_idx:]

    with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
        f.write(content)
