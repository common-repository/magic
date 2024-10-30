<?php
/**
 * Plugin Name: Magic
 * Plugin URI: https://klaetke.com/wordpress-magic-plugin
 * Description: Thanks for using the Magic Plugin. With the Following Shortcode you get the Magical Star Unicode Symbol with a awesome Click Aninmation. [magical_star], become a Magical Gradient Background with Animation [magical_gradient] A Magical Color Switcher you get with the Shortcode [magical_color]
 * Version: 1.3
 * Text Domain: klaetke.com
 * Author: Toni Klätke
 * Author URI: https://klaetke.com/
 */
 
 function magic_one($atts) {
	$Content .= '<style>

* {
  box-sizing: border-box;
  position: relative;
}


.treat-button {
  font-size: 27px;
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  background: linear-gradient(to bottom, #F46001, #E14802);
  border: none;
  color: #FFF;
  border-radius: 2em;
  padding: 0.6em 1.5em;
  overflow: hidden;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  cursor: pointer;
  z-index: 1;
  box-shadow: 0 0 1em rgba(255, 255, 255, 0.2);
  transition: transform 0.1s cubic-bezier(0.5, 0, 0.5, 1), box-shadow 0.2s;
  outline: none;
    position: absolute;
    left: 41vw;
    margin-top: 28%;
}
.treat-button:hover {
  box-shadow: 0 0 2em rgba(255, 255, 255, 0.3);
}
.treat-button:active {
  transform: scale(0.8) translateY(10%);
  transition-timing-function: cubic-bezier(0.5, 0, 0.5, 1);
}

.treat {
  --scale-x: 0;
  --scale-y: 0;
  pointer-events: none;
  display: block;
  position: absolute;
  top: 0;
  left: calc(50% - .5rem);
  border-radius: 50%;
  width: 1em;
  height: 1em;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 5vmin;
  transform: translate(calc( var(--x) * 1px ), calc( var(--y) * 1px )) translate(-50%, -50%);
  pointer-events: none;
  -webkit-animation: treat-enter 0.1s ease-in backwards, treat-exit 300ms linear calc( (var(--lifetime, 3000) * 1ms) - 300ms) forwards;
          animation: treat-enter 0.1s ease-in backwards, treat-exit 300ms linear calc( (var(--lifetime, 3000) * 1ms) - 300ms) forwards;
}
@-webkit-keyframes treat-enter {
  from {
    opacity: 0;
  }
}
@keyframes treat-enter {
  from {
    opacity: 0;
  }
}
@-webkit-keyframes treat-exit {
  to {
    opacity: 0;
  }
}
@keyframes treat-exit {
  to {
    opacity: 0;
  }
}
.treat .inner {
  -webkit-animation: inner-rotate 0.6s linear infinite;
          animation: inner-rotate 0.6s linear infinite;
  transform: rotate(calc(-1turn * var(--direction) ));
}
@-webkit-keyframes inner-rotate {
  to {
    transform: none;
  }
}
@keyframes inner-rotate {
  to {
    transform: none;
  }
}
</style>
<span class="treat-wrapper">
  <a class="star">⭐</a>
</span>
<script>console.clear();

let width = window.innerWidth;
let height = window.innerHeight;
const body = document.body;

const elButton = document.querySelector(".star");
const elWrapper = document.querySelector(".treat-wrapper");

function magic_getRandomArbitrary(min, max) {
  return Math.random() * (max - min) + min;
}

function magic_getRandomInt(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

const treatmojis = ["⭐", "⭐", "⭐", "⭐", "⭐", "⭐", "⭐"];
const treats = [];
const radius = 15;

const Cd = 0.47; // Dimensionless
const rho = 1.22; // kg / m^3
const A = Math.PI * radius * radius / 10000; // m^2
const ag = 9.81; // m / s^2
const frameRate = 1 / 60;

function magic_createTreat() /* create a treat */{
  const vx =  magic_getRandomArbitrary(-10, 10); // x velocity
  const vy =  magic_getRandomArbitrary(-10, 1); // y velocity

  const el = document.createElement("div");
  el.className = "treat";

  const inner = document.createElement("span");
  inner.className = "inner";
  inner.innerText = treatmojis[magic_getRandomInt(0, treatmojis.length - 1)];
  el.appendChild(inner);

  elWrapper.appendChild(el);

  const rect = el.getBoundingClientRect();

  const lifetime = magic_getRandomArbitrary(2000, 3000);

  el.style.setProperty("--lifetime", lifetime);

  const treat = {
    el,
    absolutePosition: { x: rect.left, y: rect.top },
    position: { x: rect.left, y: rect.top },
    velocity: { x: vx, y: vy },
    mass: 0.1, //kg
    radius: el.offsetWidth, // 1px = 1cm
    restitution: -.7,

    lifetime,
    direction: vx > 0 ? 1 : -1,

    animating: true,

    remove() {
      this.animating = false;
      this.el.parentNode.removeChild(this.el);
    },

    animate() {
      const treat = this;
      let Fx =
      -0.5 *
      Cd *
      A *
      rho *
      treat.velocity.x *
      treat.velocity.x *
      treat.velocity.x /
      Math.abs(treat.velocity.x);
      let Fy =
      -0.5 *
      Cd *
      A *
      rho *
      treat.velocity.y *
      treat.velocity.y *
      treat.velocity.y /
      Math.abs(treat.velocity.y);

      Fx = isNaN(Fx) ? 0 : Fx;
      Fy = isNaN(Fy) ? 0 : Fy;

      // Calculate acceleration ( F = ma )
      var ax = Fx / treat.mass;
      var ay = ag + Fy / treat.mass;
      // Integrate to get velocity
      treat.velocity.x += ax * frameRate;
      treat.velocity.y += ay * frameRate;

      // Integrate to get position
      treat.position.x += treat.velocity.x * frameRate * 100;
      treat.position.y += treat.velocity.y * frameRate * 100;

      treat.checkBounds();
      treat.update();
    },

    checkBounds() {

      if (treat.position.y > height - treat.radius) {
        treat.velocity.y *= treat.restitution;
        treat.position.y = height - treat.radius;
      }
      if (treat.position.x > width - treat.radius) {
        treat.velocity.x *= treat.restitution;
        treat.position.x = width - treat.radius;
        treat.direction = -1;
      }
      if (treat.position.x < treat.radius) {
        treat.velocity.x *= treat.restitution;
        treat.position.x = treat.radius;
        treat.direction = 1;
      }

    },

    update() {
      const relX = this.position.x - this.absolutePosition.x;
      const relY = this.position.y - this.absolutePosition.y;

      this.el.style.setProperty("--x", relX);
      this.el.style.setProperty("--y", relY);
      this.el.style.setProperty("--direction", this.direction);
    } };


  setTimeout(() => {
    treat.remove();
  }, lifetime);

  return treat;
}


function magic_animationLoop() {
  var i = treats.length;
  while (i--) {
    treats[i].animate();

    if (!treats[i].animating) {
      treats.splice(i, 1);
    }
  }

  requestAnimationFrame(magic_animationLoop);
}

magic_animationLoop();

function magic_addTreats() {
  //cancelAnimationFrame(frame);
  if (treats.length > 40) {
    return;
  }
  for (let i = 0; i < 10; i++) {
    treats.push(magic_createTreat());
  }
}

elButton.addEventListener("click", magic_addTreats);
elButton.click();

window.addEventListener("resize", () => {
  width = window.innerWidth;
  height = window.innerHeight;
});</script>';
	 
    return $Content;
}

add_shortcode('magical_star', 'magic_one');

function magic_two($atts) {
	$Content .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<style>	body{
	 background-color: #000000;
   padding: 0px;
   margin: 0px;
 }

#gradient
{
  width: 100vw;
  height: 100vh;
  padding: 0px;
  margin: 0px;
}</style>
<div id="gradient" />
<script>
var colors = new Array(
  [62,35,255],
  [60,255,60],
  [255,35,98],
  [45,175,230],
  [255,0,255],
  [255,128,0]);

var step = 0;
//color table indices for: 
// current color left
// next color left
// current color right
// next color right
var colorIndices = [0,1,2,3];

//transition speed
var gradientSpeed = 0.002;

function updateGradient()
{
  
  if ( $===undefined ) return;
  
var c0_0 = colors[colorIndices[0]];
var c0_1 = colors[colorIndices[1]];
var c1_0 = colors[colorIndices[2]];
var c1_1 = colors[colorIndices[3]];

var istep = 1 - step;
var r1 = Math.round(istep * c0_0[0] + step * c0_1[0]);
var g1 = Math.round(istep * c0_0[1] + step * c0_1[1]);
var b1 = Math.round(istep * c0_0[2] + step * c0_1[2]);
var color1 = "rgb("+r1+","+g1+","+b1+")";

var r2 = Math.round(istep * c1_0[0] + step * c1_1[0]);
var g2 = Math.round(istep * c1_0[1] + step * c1_1[1]);
var b2 = Math.round(istep * c1_0[2] + step * c1_1[2]);
var color2 = "rgb("+r2+","+g2+","+b2+")";

 $("#gradient").css({
   background: "-webkit-gradient(linear, left top, right top, from("+color1+"), to("+color2+"))"}).css({
    background: "-moz-linear-gradient(left, "+color1+" 0%, "+color2+" 100%)"});
  
  step += gradientSpeed;
  if ( step >= 1 )
  {
    step %= 1;
    colorIndices[0] = colorIndices[1];
    colorIndices[2] = colorIndices[3];
    
    //pick two new target color indices
    //do not pick the same as the current one
    colorIndices[1] = ( colorIndices[1] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;
    colorIndices[3] = ( colorIndices[3] + Math.floor( 1 + Math.random() * (colors.length - 1))) % colors.length;
    
  }
}

setInterval(updateGradient,10);</script>';
	 
    return $Content;
}

add_shortcode('magical_gradient', 'magic_two');

 function magic_randomcolor($atts) {
	$Content .= '<style>html {
  height: 100%;
}

body {
  width: 100%;
  height: 100%;
}

/* Animation Layer */
body, button#change {
  transition: all 0.4s ease-in-out;
}</style>
<h1 id="colour">#e63c44</h1>
  <button class="ct-button" id="change">New Colour</button>
<script>var btn = document.getElementById("change");
var text = document.getElementById("colour");

var generator = function() {
  newColour = "#"+(Math.random()*0xFFFFFF<<0).toString(16);
  console.log(newColour.length);
  if(newColour.length < 7) {
    generator();
  }
}

btn.addEventListener("click", function() {
    generator();
    
    document.body.style.background = newColour;
    btn.style.color = newColour;
    text.innerText = newColour;
});

// dh = $(document).height();

// $("#container").css({"padding-top":dh/3});

// $("#change").click(function() {
//     newColour = "#"+(Math.random()*0xFFFFFF<<0).toString(16);
//     $("body").css({"background":newColour});
//     $("#change").css({"color":newColour});
//     $("#colour").text(newColour);
// });</script>';
	 
    return $Content;
}

add_shortcode('magical_color_code', 'magic_randomcolor');

function magic_randomcolor2($atts) {
	$Content .= '<style>html {
  height: 100%;
}

body {
  width: 100%;
  height: 100%;
}

/* Animation Layer */
body, button#change {
  transition: all 0.4s ease-in-out;
}</style>
<b style="display:none;" id="colour">#e63c44</b>
  <button class="ct-button" id="change">New Colour</button>
<script>var btn = document.getElementById("change");
var text = document.getElementById("colour");

var generator = function() {
  newColour = "#"+(Math.random()*0xFFFFFF<<0).toString(16);
  console.log(newColour.length);
  if(newColour.length < 7) {
    generator();
  }
}

btn.addEventListener("click", function() {
    generator();
    
    document.body.style.background = newColour;
    btn.style.color = newColour;
    text.innerText = newColour;
});

// dh = $(document).height();

// $("#container").css({"padding-top":dh/3});

// $("#change").click(function() {
//     newColour = "#"+(Math.random()*0xFFFFFF<<0).toString(16);
//     $("body").css({"background":newColour});
//     $("#change").css({"color":newColour});
//     $("#colour").text(newColour);
// });</script>';
	 
    return $Content;
}

add_shortcode('magical_color', 'magic_randomcolor2');