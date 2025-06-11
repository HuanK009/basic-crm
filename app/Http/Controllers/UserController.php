<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function update(Request $request)
    {
        $user = User::find($request->user()->id)->with(['additionalInfo', 'spouseInfo', 'personalPref'])->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        switch ($request->section) {
            case 'additional_info':
                $rule = [
                    'additional_info.address'       => 'required|string',
                    'additional_info.country'       => 'required|string',
                    'additional_info.postal_code'   => 'required|string',
                    'additional_info.date_of_birth' => 'required|date',
                    'additional_info.gender'        => 'required|in:male,female,other',
                    'additional_info.marital_status' => 'required|in:single,married',
                ];
                break;
            case 'spouse_detail':
                $rule = [
                    'additional_info.address'       => 'required|string',
                    'additional_info.country'       => 'required|string',
                    'additional_info.postal_code'   => 'required|string',
                    'additional_info.date_of_birth' => 'required|date',
                    'additional_info.gender'        => 'required|in:male,female,other',
                    'additional_info.marital_status' => 'required|in:single,married',
                    'spouse_info.salutation' => 'required|string',
                    'spouse_info.first_name' => 'required|string',
                    'spouse_info.last_name'  => 'required|string',
                ];
                break;
            case 'personal_pref':
                $rule = [
                    'personal_pref.hobbies' => 'nullable|string',
                    'personal_pref.sports'  => 'nullable|string',
                    'personal_pref.music'   => 'nullable|string',
                    'personal_pref.movies'  => 'nullable|string',
                ];
                break;
            default:
                $rule = [
                    'salutation'    => 'required|string',
                    'first_name'    => 'required|string|max:255',
                    'last_name'     => 'required|string|max:255',
                    'email'         => 'required|email|unique:users,email,' . $user->id,
                ];
                break;
        }

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        switch ($request->section) {
            case 'additional_info':
                $user->additionalInfo()->updateOrCreate([], $request->additional_info);
                $user->save();
                break;
            case 'spouse_detail':
                $user->additionalInfo()->updateOrCreate([], $request->additional_info);
                $user->spouseInfo()->updateOrCreate([], $request->spouse_info);
                $user->save();
                break;
            case 'personal_pref':
                $user->personalPref()->updateOrCreate([], $request->personal_pref);
                $user->save();
                break;
            default:
                $user->update([
                    'salutation' => $request->salutation,
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'email'      => $request->email,
                ]);


                break;
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ], 200);
    }
    public function upload_avatar(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Store the new avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        // Optionally delete the old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Update the user's avatar path
        $user->avatar = 'storage/' . $path;
        $user->save();

        return response()->json([
            'message' => 'Avatar uploaded successfully',
            'avatar_url' => Storage::url($path),
        ], 200);
    }
}
