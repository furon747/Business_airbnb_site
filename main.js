
if(document.readyState !== 'loading')
{
   addListeners();
}
else 
{
    document.addEventListener('DOMContentLoaded', () =>{
        addListeners();
    })
}

function addListeners()
{

}

function $(x) {
    return document.getElementById(x);
}