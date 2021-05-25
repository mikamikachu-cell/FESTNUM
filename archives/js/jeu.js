/* console.log("coucou"); */
let sprite = document.querySelector('#cigogne')
//  je récupère l'id de la cigogne
console.log('cigogne', sprite)
console.log('cigogne top', sprite.style.top)
console.log('cigogne left', sprite.style.left)

let positionX = 0
let positionY = 0
// acclération
let accelX = 0
let accelY = 0
sprite.style.left = positionX + 'px'
sprite.style.top = positionY + 'px'

//je créé une constante pour les touches directionnelles du clavier
const CLAVIER_DROIT = 39
const CLAVIER_GAUCHE = 37
const CLAVIER_HAUT = 38
const CLAVIER_BAS = 40
const LARGEUR_GIF = 220
const HAUTEUR_GIF = 200
/* const LARGEUR_GIF_GAUCHE = -1 */

function move(e) {
    // e.preventDefault() // permet de désactiver le comportement par défaut du navigateur (descente dans la page / scrollbar)
    let hitBox = document.querySelector('#fondBleu')
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
        console.log(positionY, window.innerHeight);

    }

    if (e.keyCode == CLAVIER_BAS) {
        if (positionY > 80) {
            return
        }
        positionY -= -5
        accelY = accelY - 1
        sprite.style.top = positionY + 'px'
    }
}

function reset() {
    accelX = 0
}
document.onkeydown = move
document.onkeyup = reset

function estEnCollision(a, b) {
    let cigogne = document.getElementsById('cigogne')
    let bobine = document.getElementsByClassName('bobine')

    if (((cigogne.top + cigogne.height) < (bobine.top)) ||
        (cigogne.top > (bobine.top + bobine.height)) ||
        ((cigogne.left + cigogne.width) < bobine.left) ||
        (cigogne.left > (bobine.left + bobine.width))) { console.log('objet detecté') }

}


// https://stackoverflow.com/questions/2440377/javascript-collision-detection
//https://developer.mozilla.org/fr/docs/Games/Techniques/2D_collision_detection