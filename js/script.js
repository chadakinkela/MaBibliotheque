const searchInput = document.getElementById("searchInput");
const resultPopup = document.getElementById("resultPopup");

//affichage automatique de données lorsqu'on clique dans la barre de recherche
searchInput.addEventListener("keyup", () => {

    let value = searchInput.value.trim();

    if (value.length === 0) {
        resultPopup.style.display = "none";
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open("GET", "results.php?search=" + encodeURIComponent(value), true);

    xhr.onload = () => {
        if (xhr.status === 200) {
            resultPopup.innerHTML = xhr.responseText;
            resultPopup.style.display = "block";
        }
    };

    xhr.send();
});

// Fermer quand on clique ailleurs
document.addEventListener("click", function(e) {
    if (!resultPopup.contains(e.target) && e.target !== searchInput) {
        resultPopup.style.display = "none";
    }
});
