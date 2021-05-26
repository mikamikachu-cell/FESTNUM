/* console.log("coucou"); */
let sprite = document.querySelector('#cigogne')
//  je récupère l'id de la cigogne
// console.log('cigogne', sprite)
// console.log('cigogne top', sprite.style.top)
// console.log('cigogne left', sprite.style.left)

let positionX = 0
let positionY = 0
// acclération
let accelX = 0
let accelY = 0
sprite.style.left = positionX + 'px'
sprite.style.top = positionY + 'px'
let score = 0

//je créé une constante pour les touches directionnelles du clavier
const CLAVIER_DROIT = 39
const CLAVIER_GAUCHE = 37
const CLAVIER_HAUT = 38
const CLAVIER_BAS = 40
const LARGEUR_GIF = 220
const HAUTEUR_GIF = 200
/* const LARGEUR_GIF_GAUCHE = -1 */
let boutonOff = document.querySelector('.boutonOff')

function move(e) {
    // e.preventDefault() // permet de désactiver le comportement par défaut du navigateur (descente dans la page / scrollbar)
    let hitBox = document.querySelector('#fondBleu')
    let bobine = document.getElementsByClassName('bobine')
    let bobinedeux = document.getElementsByClassName('bobine2')

    if (e.keyCode == CLAVIER_DROIT) {
        if (hitBox.offsetWidth < positionX + LARGEUR_GIF) {
            return
        }
        /* console.log(accelX)
        console.log(positionX) */
        accelX = accelX + 1
        positionX += accelX
        sprite.style.left = positionX + 'px'
        //je fais un miroir de la cigogne quand elle tourne à gauche
        sprite.classList.remove('mirror')
    }

    if (e.keyCode == CLAVIER_GAUCHE) {
        if (positionX < LARGEUR_GIF / -4) {
            return
        }
        positionX += -5
        accelX = accelX + 1
        positionX -= accelX
        sprite.style.left = positionX + 'px'
        sprite.classList.add('mirror')
    }

    if (e.keyCode == CLAVIER_HAUT) {
        if (positionY < -45) {
            return
        }
        positionY -= 5
        accelY = accelY + 1
        sprite.style.top = positionY + 'px'
        // console.log(positionY, window.innerHeight);

    }

    if (e.keyCode == CLAVIER_BAS) {
        if (positionY > 80) {
            return
        }
        positionY -= -5
        accelY = accelY - 1
        sprite.style.top = positionY + 'px'
    }

    let mika = sontEnCollision()
    // console.log('collision ?', mika)
}

/**
 * 
 * @returns true si une collision est détectée entre la cigogne et la bobine
 */
function sontEnCollision() {
    let cigogne = document.getElementById('cigogne')
    let bobine = document.getElementsByClassName('bobine2')[0]
    let bobinedeux = document.getElementsByClassName('bobine1')[0]
    // console.log('collision', cigogne.top, cigogne.height);
    // if (((cigogne.top + cigogne.height) < (bobine.top)) ||
    //     (cigogne.top > (bobine.top + bobine.height)) ||
    //     ((cigogne.left + cigogne.width) < bobine.left) ||
    //     (cigogne.left > (bobine.left + bobine.width))) { return true }
    // return false

    // let test = new HitTest(cigogne)

    // if (test.toObject(bobine)) {
    //     // Collision detected
    //     console.log("touché !!");
    // }
    let bodyRect = document.body.getBoundingClientRect(),
        cigRect = cigogne.getBoundingClientRect(),
        cigOffset = cigRect.left - bodyRect.left,
        bobineRect = bobine.getBoundingClientRect(),
        bobineOffset = Math.floor(bobineRect.left - bodyRect.left);

    bobinedeuxRect = bobinedeux.getBoundingClientRect(),
        bobinedeuxOffset = Math.floor(bobinedeuxRect.left - bodyRect.left);

    // console.log(cigOffset, bobineOffset);
    if (cigOffset <= bobineOffset + 80 && cigOffset >= bobineOffset - 50) {
        // console.log('touché')

        bobine.classList.add('d-none')
        // quand l'oiseau touche la bobine cela ajoute 1 au score
        document.querySelector('.score').innerHTML = 'score : ' + score++
        arreteJeu()
    }
    if (cigOffset <= bobinedeuxOffset + 80 && cigOffset >= bobinedeuxOffset - 50) {
        bobinedeux.classList.add('d-none')
    }
}

let boutonDemarrer = document.querySelector('#fondBleu .boutonPlay')
// console.log('bout', boutonDemarrer)
// document.onkeyup = reset

function demarrerJeu() {
    document.onkeydown = move
    boutonDemarrer.classList.add('d-none')
    //quand la mouette dépasse l'écran on ne la voit plus
    document.body.classList.add('overflow-hidden')
}
//quand je clique sur le bouton le jeu démarre
boutonDemarrer.addEventListener('click', demarrerJeu)

function arreteJeu() {
    boutonDemarrer.classList.remove('display')
    document.onkeydown = null
}
// quand je clique sur le bouton avec la croix le jeu redemarre


function redemarrerJeu() {
    // console.log("restart game");
    let bobine = document.getElementsByClassName('bobine2')[0]

    // console.log(bobine);
    bobine.classList.remove('d-none')
    boutonDemarrer.classList.remove('d-none')

    sprite.style.left = '0px'
    sprite.style.top = '0px'
    positionX = 0
    positionY = 0
    // console.log(sprite);

}
boutonOff.addEventListener('click', redemarrerJeu)





