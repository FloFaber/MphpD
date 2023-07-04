/*
 * Copyright (c) 2023 Florian Faber
 * http://www.flofaber.com
 */

function str2slug(str){
    return str.toLowerCase()
        .replace(/ /g, '-')
        .replace(/[^\w-]+/g, '')
        .replace(/-$/, '')
}

let headings = document.querySelectorAll("h1, h2, h3, h4, h5, h6");
for(let i = 0; i < headings.length; i++){
    let h = headings[i];
    let slug = str2slug(h.innerText);
    h.setAttribute("data-url", slug);
    h.innerHTML += "<span class='heading-url'><a href='" + window.location.pathname + "#" + slug + "'>🔗</a></span>";
}

function jump2heading(){
    let hash = window.location.hash.replace("#", "");
    let el = document.querySelector("[data-url='"+hash+"']");
    if(hash !== "" && el){
        el.scrollIntoView({ behavior: 'smooth' });
    }
}

window.onpopstate = function(){
    jump2heading();
}

// wait a small amount of time before jumping to the referred heading because sometimes it just does not work without it.
setTimeout(function(){
    jump2heading();
},20);
