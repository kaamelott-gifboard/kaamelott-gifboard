window.onload = function(){
    let xhr = new XMLHttpRequest();
    let select = document.getElementById("characters");

    xhr.onload = function () {
        if (xhr.status >= 200 && xhr.status < 300) {
            let data = JSON.parse(xhr.responseText);

            data['characters'].forEach(function (character) {
                let option = document.createElement("option");

                option.text = character;
                option.value = character;

                if (character === select.getAttribute('data-current')) {
                    option.selected = true;
                }

                select.appendChild(option);
            });
        } else {
            // @todo: handle error
        }

    };

    xhr.open('GET', select.getAttribute('data-url'));
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    xhr.send();
};

// ==================================================

function showGif(e) {
    document.getElementById("modal").style.display = "block";

    let gif = document.getElementById(e.getAttribute('data-id'));

    document.getElementById("modal-img").src = gif.getAttribute('data-img');
    document.getElementById("modal-quote").innerText = gif.getAttribute('data-quote');
    document.getElementById("modal-link").innerText = gif.getAttribute('data-url');
}

// ==================================================

let modal = document.getElementById("modal");

document.getElementsByClassName("modal-close")[0].onclick = function() {
    modal.style.display = "none";
}

window.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        modal.style.display = 'none'
    }
})

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
