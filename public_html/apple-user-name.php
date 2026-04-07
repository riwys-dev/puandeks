<?php
session_start();

if (!isset($_SESSION['apple_complete_profile'])) {
    header("Location: /login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Apple Profil Bilgileri</title>

<style>

*{
box-sizing:border-box;
}

body{
font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
background:#f7f7f7;
display:flex;
align-items:center;
justify-content:center;
height:100vh;
margin:0;
padding:20px;
}

/* container */

.box{
background:#fff;
padding:40px;
border-radius:10px;
width:100%;
max-width:420px;
}

/* logo */

.logo{
display:block;
margin:0 auto 25px auto;
height:34px;
}

/* title */

h2{
text-align:center;
font-size:22px;
margin:0 0 10px 0;
font-weight:600;
}

/* subtitle */

.subtitle{
text-align:center;
font-size:14px;
color:#666;
margin-bottom:30px;
}

/* inputs */

input{
width:100%;
padding:14px;
margin-bottom:15px;
border:1px solid #ddd;
border-radius:8px;
font-size:15px;
}

/* button */

button{
width:100%;
padding:14px;
border:none;
background:#1C1C1C;
color:#fff;
font-size:15px;
border-radius:8px;
cursor:pointer;
transition:0.2s;
}

button:disabled{
background:#bbb;
cursor:not-allowed;
}

button:hover:not(:disabled){
opacity:0.9;
}

/* responsive */

@media(max-width:480px){

.box{
padding:30px 20px;
}

h2{
font-size:20px;
}

}

</style>

</head>

<body>

<div class="box">

<img src="/img/puandeks-logo_2.svg" class="logo">

<h2>Apple profil bilgilerinizi tamamlayın</h2>

<div class="subtitle">
Apple üyeliğiniz ile ilişkili adınızı ve soyadınızı girin.
</div>

<form id="appleNameForm">

<input
type="text"
name="name"
placeholder="Adınız"
required
>

<input
type="text"
name="surname"
placeholder="Soyadınız"
required
>

<button type="submit" id="submitBtn" disabled>
Devam Et
</button>

</form>

</div>

<script>

const form = document.getElementById("appleNameForm");
const button = document.getElementById("submitBtn");

const nameInput = form.querySelector("input[name=name]");
const surnameInput = form.querySelector("input[name=surname]");

function checkInputs(){

if(nameInput.value.trim() !== "" && surnameInput.value.trim() !== ""){
button.disabled = false;
}else{
button.disabled = true;
}

}

nameInput.addEventListener("input", checkInputs);
surnameInput.addEventListener("input", checkInputs);

form.addEventListener("submit", async function(e){

e.preventDefault();

const formData = new FormData(form);

const response = await fetch("/api/apple-user-name.php",{
method:"POST",
body:formData
});

const data = await response.json();

if(data.success){

window.location.href="/";

}else{

alert("Bir hata oluştu.");

}

});

</script>

</body>
</html>