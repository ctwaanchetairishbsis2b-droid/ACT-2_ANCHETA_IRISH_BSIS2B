const btn = document.getElementById("profileBtn");
const menu = document.getElementById("profileMenu");

btn.addEventListener("click", function(e){
    e.stopPropagation();

    if(menu.style.display === "block"){
        menu.style.display = "none";
    }else{
        menu.style.display = "block";
    }
});

document.addEventListener("click", function(){
    menu.style.display = "none";

    
document.getElementById("profileBtn").onclick = function () {
    document.getElementById("profileMenu")
    .classList.toggle("show");
};

});