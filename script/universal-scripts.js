function rindirizza(pagina) {
    window.location.href = pagina + '.php';
}


document.getElementById("scrollToFilters").addEventListener("click", function (event) {
    event.preventDefault(); // Evita il comportamento predefinito del link
    const filtersSection = document.getElementById("filtri-container");
    filtersSection.scrollIntoView({ behavior: "smooth" });
});






