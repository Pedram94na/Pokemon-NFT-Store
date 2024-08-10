const form = document.querySelector("#filter_form");

console.log(form);

form.addEventListener("submit", (e) => {
    e.preventDefault();

    fetch('url_data.json')
        .then(response => response.json())
        .then(data => {
            //console.log(data);

            const urlData = data.url_data;
            //window.location.href = 'index.php' + urlData;
        })
        .catch(error => console.error('Error fetching JSON data:', error));
});