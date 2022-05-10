let $open = document.querySelector('.open-modale-slide-side');
let $close = document.querySelector('.close');
let $modale = document.querySelector('.modaleContainer');

$open.addEventListener('click', function(){
    $modale.style.display = "block";
});

$close.addEventListener('click', function(){
    $modale.style.display = "none";
});