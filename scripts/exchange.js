const urlParams = new URLSearchParams(window.location.search);
const request = urlParams.get('sent_request');

//console.log('Replace:', replaceValue);

if (request === "successful")
    alert("Exchange request has been sent successfully!");

else if (request === "failed")
    alert("ERROR! Exchange request failed!")