window.onload = function(){
    let characterXhr = new XMLHttpRequest();
    let select = document.getElementById("characters");

    characterXhr.onload = function () {
        if (characterXhr.status >= 200 && characterXhr.status < 300) {
            let data = JSON.parse(characterXhr.responseText);

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

    characterXhr.open('GET', select.getAttribute('data-url'));
    characterXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    characterXhr.send();

    // ==================================================

    let countXhr = new XMLHttpRequest();
    let input = document.getElementById("quotes");

    countXhr.onload = function () {
        if (countXhr.status >= 200 && countXhr.status < 300) {
            let data = JSON.parse(countXhr.responseText);

            input.placeholder = 'Parmi près de '.concat(data, ' répliques...');
        } else {
            // @todo: handle error
        }
    };

    countXhr.open('GET', input.getAttribute('data-url'));
    countXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
    countXhr.send();
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
