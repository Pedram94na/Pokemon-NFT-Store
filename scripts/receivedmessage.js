const urlParams = new URLSearchParams(window.location.search);
const message = urlParams.get('message_received');

//console.log('Replace:', replaceValue);

if (message === "exchange_request")
    alert("An exchange request has been submitted!");