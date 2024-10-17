const io = require('socket.io')(1111,{ cors: { origin: '*' }})


let clients = new Map()

io.on('connection', socket=>{ 
	socket.on('online', (id)=>{
		console.info(`Client online [id=${id}]`);
		clients.set(socket, id)
	})
	socket.on("disconnect", () => {
		var id = clients.get(socket)
        clients.delete(socket);
        console.info(`Client offline [id=${id}]`);
    });

    /*???*/
    socket.on('send_notification', (sender_id, target_id, text)=>{
    	for (const [client, id] of clients.entries()) {
    		console.info(`${sender_id} ${target_id} ${text}`)
    		if(id ==  target_id){
	        	client.emit("notification", sender_id, text);
	        	break; 
    		}
    	};
    });


    /*SOMME ONE SENT A ADD REQUESTE*/
    socket.on('send_add', (uid, text)=>{ 
    	for (const [client, id] of clients.entries()) {
    		if(id ==  uid){
	        	client.emit("receive_add",uid, text);
	        	break; 
    		}
    	};
    });
    

    /* SOMME FRIEND SEND INVITATION FOR WATCH MOVE*/
    socket.on('send_invit', (sender_id, target_id, message)=>{ 
    	for (const [client, id] of clients.entries()) {
    		if(id ==  target_id){
	        	client.emit("receive_invit", sender_id, message);
	        	break; 
    		}
    	};
    });
})
 