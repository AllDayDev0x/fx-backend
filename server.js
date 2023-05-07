var app = require('express')();
var fs = require('fs');
var debug = require('debug')('FX:sockets');
var request = require('request');
const http = require('http')
var dotenv = require('dotenv').config();

const util = require('util');

const setTimeoutPromise = util.promisify(setTimeout);

var port = process.env.PORT || '3017';

var chat_save_url = process.env.APP_URL;

var SSL_KEY = process.env.SSL_KEY;

var SSL_CERTIFICATE = process.env.SSL_CERTIFICATE;

if( SSL_KEY && SSL_CERTIFICATE) {
    var https = require('https');
    var server = https.createServer({key: fs.readFileSync(SSL_KEY),cert: fs.readFileSync(SSL_CERTIFICATE) },app);
    server.listen(port);
} else {
    var server = require('http').Server(app);
    server.listen(port);   
}

var io = require('socket.io')(server, {cors: {origin: "*"}});

io.on('connection', function (socket) {

    console.log('new connection established');

    socket.commonid = socket.handshake.query.commonid;

    console.log(socket.commonid);

    console.log(socket.handshake.query.commonid);
    
    socket.join(socket.handshake.query.commonid);

    socket.join(socket.handshake.query.room);

    socket.emit('connected', {'sessionID' : socket.handshake.query.commonid});

    socket.on('notification update', function(data) {

        console.log('notification update', data);

        socket.handshake.query.myid = data.myid;

        socket.handshake.query.commonid = data.commonid;

        socket.commonid = socket.handshake.query.commonid;

        socket.join(socket.handshake.query.commonid);

        global.chat_notification = 0;
        global.bell_notification = 0;

        setInterval(function (){
            
            var notification_receiver = "user_id_"+data.myid;

            const url = chat_save_url+'api/user/get_notifications_count?user_id='+data.myid;

            request.get(url, function (error, response, body) {

                if(body && body != undefined){

                    const res_data = JSON.parse(body);

                    if(res_data.data && res_data.data != undefined){

                        chat_notification = res_data.data.chat_notification;
                        
                        bell_notification = res_data.data.bell_notification;

                        console.log('notification_receiver', notification_receiver);

                        let notification_data = {chat_notification:chat_notification, bell_notification:bell_notification};

                        console.log('notification_data', notification_data);

                        var notification_status = socket.broadcast.to(notification_receiver).emit('notification', notification_data);
                    }
                }
            })            

        },120000);

    });

    socket.on('update sender', function(data) {

        console.log("Update Sender START");

        console.log('update sender', data);

        socket.handshake.query.myid = data.myid;

        socket.handshake.query.commonid = data.commonid;

        socket.commonid = socket.handshake.query.commonid;

        socket.join(socket.handshake.query.commonid);

        socket.emit('sender updated', 'Sender Updated ID:'+data.myid, 'Request ID:'+data.commonid);

        console.log("Update Sender END");

    });

    socket.on('message', function(data) {

        console.log("send message Start");

        console.log("ON message", data);

        if(data.loggedin_user_id == data.from_user_id) {

            var receiver = "user_id_"+data.to_user_id+"_to_user_id_"+data.from_user_id;

        } else {

            var receiver = "user_id_"+data.from_user_id+"_to_user_id_"+data.to_user_id;
        }


        console.log('data', data);

        console.log('receiver', receiver);

        var sent_status = socket.broadcast.to(receiver).emit('message', data);

        url = chat_save_url+'api/user/chat_messages_save?from_user_id='+data.from_user_id
        +'&to_user_id='+data.to_user_id
        +'&message='+data.message
        +'&chat_message_id='+data.chat_message_id;

        const encodedURI = encodeURI(url);

        console.log(encodedURI);

        console.log('data', data.message);

        request.get(encodedURI, function (error, response, body) {

            // if(body && body != undefined){

            //     const res_data = JSON.parse(body);

            //     console.log(res_data);

            // }

        });

        console.log("send message END");

    });

    socket.on('video call update sender', function(data) {

        console.log("video call update sender START");

        console.log('video call update sender', data);

        socket.handshake.query.myid = data.myid;

        socket.handshake.query.commonid = data.commonid;

        socket.commonid = socket.handshake.query.commonid;

        socket.join(socket.handshake.query.commonid);

        socket.emit('sender updated', 'Sender Updated ID:'+data.myid, 'Request ID:'+data.commonid);

        console.log("video call update sender END");

    });

    socket.on('video call chat', function(data) {

        console.log("send message Start");

        console.log("ON message", data);

        var receiver = "video_call_"+data.video_call_unique_id;

        if(data.loggedin_user_id == data.user_id) {

            // var receiver = "model_id_"+data.model_id+"_user_id_"+data.user_id;

            var user_id = data.model_id;

            var model_id = data.user_id;

        } else {

            // var receiver = "model_id_"+data.user_id+"_user_id_"+data.model_id;

            var user_id = data.user_id;

            var model_id = data.model_id;
        }


        console.log('data', data);

        console.log('receiver', receiver);

        var sent_status = socket.broadcast.to(receiver).emit('video call chat', data);

        url = chat_save_url+'api/user/vc_chat_messages_save?user_id='+data.user_id
        +'&model_id='+data.model_id
        +'&video_call_request_id='+data.video_call_request_id
        +'&message='+data.message
        +'&loggedin_user_id='+data.loggedin_user_id;

        const encodedURI = encodeURI(url);

        console.log(encodedURI);

        console.log('data', data.message);

        request.get(encodedURI, function (error, response, body) {


        });

    });

    socket.on('update livevideo', function(data) {

        console.log("Update livevideo START");

        console.log('update livevideo', data);

        socket.handshake.query.room = data.room;

        // socket.handshake.query.reqid = data.reqid;

        // socket.reqid = socket.handshake.query.reqid;

        socket.join(socket.handshake.query.room);

        socket.emit('livevideo updated', 'Team Updated ID:'+data.room);

        console.log("Update team END");

    });

    console.log("ROOM ID"+socket.handshake.query.room);

    socket.on('livevideo message', function(data) {

        console.log("livevideo Send message",data);

        data.room = socket.handshake.query.room;

        console.log("receiver",data.room);

        socket.broadcast.to(data.room).emit('livevideo message', data);

        url = chat_save_url+'api/user/lv_chat_messages_save?user_id='+data.user_id
        +'&live_video_id='+data.live_video_id
        +'&message='+data.message;

        const encodedURI = encodeURI(url);

        console.log(encodedURI);

        console.log('data', data.message);

        request.get(encodedURI, function (error, response, body) {


        });


    });

    socket.on('livestream-connect', function(data) {

        console.log("livestream-connect START");

        console.log('livestream-connect', data);

        socket.handshake.query.room = data.room;

        socket.join(socket.handshake.query.room);

        socket.emit('livestream-connect', 'Team Updated ID:'+data.room);

        console.log("livestream-connect END");

    });

    console.log("ROOM ID"+socket.handshake.query.room);

    socket.on('livestream-broadcast-message', function(data) {

        console.log("livestream-broadcast-message start");

        console.log("livestream-broadcast-message",data);

        data.room = socket.handshake.query.room;

        console.log("receiver",data.room);

        socket.broadcast.to(data.room).emit('livestream-broadcast-message', data);

        url = chat_save_url+'api/user/lv_chat_messages_save?user_id='+data.user_id
        +'&live_video_id='+data.live_video_id
        +'&message='+data.message;

        const encodedURI = encodeURI(url);

        console.log(encodedURI);

        console.log('data', data.message);

        request.get(encodedURI, function (error, response, body) {


        });

        console.log("livestream-broadcast-message end");

    });

    socket.on('livestream-updates', function(data) {

        console.log("livevideo updates - Start");

        console.log("livevideo updates",data);

        data.room = socket.handshake.query.room;

        console.log("receiver",data.room);

        socket.broadcast.to(data.room).emit('livestream-updates', data);

        console.log("livevideo updates - END");

    });

    socket.on('livestream-join', function(data) {

        console.log("livestream-join START");

        console.log("livestream-join",data);

        data.room = socket.handshake.query.room;

        console.log("receiver",data.room);

        url = chat_save_url+'api/user/lv_viewer_update?live_video_id='+data.live_video_id
        +'&viewer_id='+data.viewer_id;

        console.log(url);

        request.get(url, function (error, response, body) {

            console.error('error:', error); // Print the error if one occurred

            console.log('statusCode:', response && response.statusCode); // Print the response status code if a response was received
            
            // console.log('body:', body); // Print the HTML for the Google homepage.

            if(body && body != undefined){

                const res_data = JSON.parse(body);

                if(res_data.data && res_data.data != undefined){

                    let updateData = data;

                    updateData.viewer_cnt = res_data.data.viewer_cnt;
                    
                    updateData.total_earnings = res_data.data.total_earnings;

                    updateData.total_earnings_formatted = res_data.data.total_earnings_formatted;

                    console.log(updateData);
                    
                    socket.broadcast.to(data.room).emit('livestream-updates', updateData);

                }
            }

        });

        console.log("livestream-join END");

    });

    socket.on('livestream-exit', function(data) {

        console.log("livestream-exit START");

        console.log("livestream-exit",data);

        data.room = socket.handshake.query.room;

        console.log("receiver",data.room);

        socket.broadcast.to(data.room).emit('livestream-updates', data);

        console.log("livestream-exit END");

    });

    socket.on('audio call update sender', function(data) {

        console.log("audio call update sender START");

        console.log('audio call update sender', data);

        socket.handshake.query.myid = data.myid;

        socket.handshake.query.commonid = data.commonid;

        socket.commonid = socket.handshake.query.commonid;

        socket.join(socket.handshake.query.commonid);

        socket.emit('sender updated', 'Sender Updated ID:'+data.myid, 'Request ID:'+data.commonid);

        console.log("audio call update sender END");

    });

    socket.on('audio call chat', function(data) {

        console.log("send message Start");

        console.log("ON message", data);

        var receiver = "audio_call_"+data.audio_call_unique_id;

        if(data.loggedin_user_id == data.user_id) {

            // var receiver = "model_id_"+data.model_id+"_user_id_"+data.user_id;

            var user_id = data.model_id;

            var model_id = data.user_id;

        } else {

            // var receiver = "model_id_"+data.user_id+"_user_id_"+data.model_id;

            var user_id = data.user_id;

            var model_id = data.model_id;
        }


        console.log('data', data);

        console.log('receiver', receiver);

        var sent_status = socket.broadcast.to(receiver).emit('audio call chat', data);

        url = chat_save_url+'api/user/ac_chat_messages_save?user_id='+data.user_id
        +'&model_id='+data.model_id
        +'&audio_call_request_id='+data.audio_call_request_id
        +'&message='+data.message
        +'&loggedin_user_id='+data.loggedin_user_id;

        const encodedURI = encodeURI(url);

        console.log(encodedURI);

        console.log('data', data.message);

        request.get(encodedURI, function (error, response, body) {


        });


        console.log("send message END");

    });

    socket.on('disconnect', function(data) {

        console.log('disconnect', data);
    });
});