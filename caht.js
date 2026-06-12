const socket = io();

function sendMessage(){

    let input =
    document.getElementById("messageInput");

    let message =
    input.value;

    socket.emit(
        "chat message",
        message
    );

    input.value = "";
}

socket.on(
    "chat message",
    function(msg){

        let div =
        document.getElementById("messages");

        div.innerHTML +=
        `<p>${msg}</p>`;

        div.scrollTop =
        div.scrollHeight;
    }
);