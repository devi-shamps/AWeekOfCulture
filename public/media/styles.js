const cursor = document.querySelector('.cursor');

document.addEventListener('mousemove', e => {
    cursor.setAttribute("style","top: "+(e.pageY-6)+"px; left: "+(e.pageX-6)+"px;")
})

document.addEventListener('click' ,() => {
    cursor.classList.add("expand");

    setTimeout(() =>{
        cursor.classList.remove("expand");
    }, 500)
})