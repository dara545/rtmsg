<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrivateMessage;

class PrivateMessageController extends Controller
{
    /*
    public function __construct() {
        $this->middleware('auth:api');
    }
    */

    public function getUserNotifications(Request $request) {
        $notifications = PrivateMessage::where('read', 0)
            ->where('receiver_id', $request->user()->id)
            ->orderBy('created_at','desc')->get();

        return response(['data'=>$notifications], 200);
    }

    public function getPrivateMessages(Request $request) {
        $msgs = PrivateMessage::where('receiver_id', $request->user()->id)
            ->orderBy('crated_at','desc')->get();
        return response(['data'=>$msgs], 200);
    }

    public function getPrivateMessageById(Request $request) {
        $msg = PrivateMessage::where('id', $request->input('id'))->first();
        // if the message is not read, change the status
        if ($msg->read == 0) {
            $msg->read = 1;
            $msg->save();
        }
        return response(['data'=>$msg], 200);
    }

    public function getPrivateMessageSent() {
        $msg = PrivateMessage::where('sender_id', $request->user()->id)
            ->orderBy('created_at','desc')->get();
        return response(['data'=>$msg], 200);
    }

    public function sendPrivateMessage() {
        $attributes = [
            'sender_id'=>$request->input('sender_id'),
            'receiver_id'=>$request->input('receiver_id'),
            'subject'=>$request->input('subject'),
            'message'=>$request->input('message'),
            'read'=> 0,
        ];
        $msg = PrivateMessage::create($attributes);
        $data = PrivateMessage::where('id', $msg->id)->first();
        return response(['data'=>$data], 201);
    }
}
