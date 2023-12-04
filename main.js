var expandedImgToGet;
if(document.readyState !== 'loading')
{
   addListeners();
}
else 
{
    document.addEventListener('DOMContentLoaded', () =>{
        //addListeners();
    })
}

function addListeners()
{

}
function myFunction(imgs, id) {
    // Get the expanded image
    let string = "expandedImg".concat('-', id.substring(0, id.indexOf("-")));
    var expandImg = document.getElementById(string);
    // Get the image text
    var imgText = document.getElementById(id);
    // Use the same src in the expanded image as the image being clicked on from the grid
    expandImg.src = imgs.src;
    // Use the value of the alt attribute of the clickable image as text inside the expanded image
    imgText.innerHTML = imgs.alt;
    // Show the container element (hidden with CSS)
    expandImg.parentElement.style.display = "inline-block";
  }

function $(x) {
    return document.getElementById(x);
}