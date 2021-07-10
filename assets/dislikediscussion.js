function onClickBtnDisLike(event) {

    event.preventDefault();

    const url = this.href;
    const spanCount = this.querySelector('span.js-dislikes');// this = a, on cherche le span
    const icone = this.querySelector('i');
    var message = this.querySelector('p');

    axios.get(url).then(function (response) {
        spanCount.textContent = response.data.dislikes;

        if (icone.classList.contains('fas'))
            icone.classList.replace('fas', 'far');
        else icone.classList.replace('far', 'fas');
    }).catch(function (error) {
        if (error.response.status === 403) {
            window.alert("Vous devez vous connecter !");
        } else {
            message.innerHTML = "Aimer ou ne pas aimer...il faut choisir !";
            setTimeout(function () {
                message.innerHTML = "";
            }, 3000);
            // window.alert("Vous ne pouvez pas à la fois aimer et ne pas aimer cette discussion");
        }
    })

}

document.querySelectorAll('a.js-dislike').forEach(function (link) {
    link.addEventListener('click', onClickBtnDisLike);
})

