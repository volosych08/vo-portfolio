import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { projects } from './projectData.js';
import { LaptopScreenUI } from './laptopScreenUi.js';

const canvas = document.getElementById('laptopCanvas');
const fullscreenWrapper = document.getElementById('laptopFullscreenWrapper');
const fullscreenButton = document.getElementById('laptopFullscreenButton');

const scene = new THREE.Scene();
scene.background = new THREE.Color(0x1f2937);

const camera = new THREE.PerspectiveCamera(
    45,
    canvas.clientWidth / canvas.clientHeight,
    0.1,
    100
);

camera.position.set(0, 1.1, 2.6);

const renderer = new THREE.WebGLRenderer({
    canvas,
    antialias: true,
});

renderer.setSize(canvas.clientWidth, canvas.clientHeight, false);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

const ambientLight = new THREE.AmbientLight(0xffffff, 1.3);
scene.add(ambientLight);

const directionalLight = new THREE.DirectionalLight(0xffffff, 2.2);
directionalLight.position.set(4, 6, 5);
scene.add(directionalLight);

const controls = new OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.enableRotate = false;
controls.enablePan = false;
controls.enableZoom = true;
controls.target.set(0, 0.15, 0);
controls.update();

const loader = new GLTFLoader();

let laptop = null;
let screenPart = null;
let screenMesh = null;
let laptopScreenUI = null;

let screenOpenProgress = 0;
let targetScreenOpenProgress = 0;

const screenClosedAngle = THREE.MathUtils.degToRad(0);
const screenOpenAngle = THREE.MathUtils.degToRad(-110);

let isDragging = false;
let previousMouseX = 0;
let previousMouseY = 0;
let suppressClick = false;

const screenCanvas = document.createElement('canvas');
screenCanvas.width = 1024;
screenCanvas.height = 640;

const screenTexture = new THREE.CanvasTexture(screenCanvas);

function findObjectByName(root, name) {
    let found = null;

    root.traverse((child) => {
        if (child.name.toLowerCase() === name.toLowerCase()) {
            found = child;
        }
    });

    return found;
}

loader.load(
    '/models/laptop.glb',
    (gltf) => {
        laptop = gltf.scene;

        scene.add(laptop);

        const box = new THREE.Box3().setFromObject(laptop);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        laptop.position.x -= center.x;
        laptop.position.y -= center.y;
        laptop.position.z -= center.z;

        const maxDimension = Math.max(size.x, size.y, size.z);
        const wantedSize = 2.1;
        const scale = wantedSize / maxDimension;

        laptop.scale.set(scale, scale, scale);
        laptop.position.y -= 0.45;

        // 0 = screen faces camera in your current setup
        laptop.rotation.y = 0;

        screenPart = findObjectByName(laptop, 'Bevels_2');
        screenMesh = findObjectByName(laptop, 'object_7');

        if (screenPart) {
            screenPart.rotation.x = screenOpenAngle;
        } else {
            console.warn('Screen part Bevels_2 not found');
        }

        if (screenMesh && screenMesh.isMesh) {
            laptopScreenUI = new LaptopScreenUI(screenCanvas, screenTexture, projects);
            laptopScreenUI.render();

            screenMesh.material = new THREE.MeshBasicMaterial({
                map: screenTexture,
                side: THREE.DoubleSide,
                toneMapped: false,
            });
        } else {
            console.warn('Screen mesh object_7 not found');
        }

        laptop.traverse((child) => {
            console.log('Object name:', child.name, 'Type:', child.type);
        });
    },
    undefined,
    (error) => {
        console.error('Error loading laptop model:', error);
    }
);

document.addEventListener('keydown', (event) => {
    if (event.code !== 'Space') {
        return;
    }

    event.preventDefault();

    if (!screenPart) {
        return;
    }

    targetScreenOpenProgress = targetScreenOpenProgress === 1 ? 0 : 1;
});

fullscreenButton.addEventListener('click', async () => {
    if (!document.fullscreenElement) {
        await fullscreenWrapper.requestFullscreen();
        fullscreenButton.textContent = 'Exit full screen';
    } else {
        await document.exitFullscreen();
        fullscreenButton.textContent = 'Full screen';
    }

    setTimeout(resizeRenderer, 150);
});

document.addEventListener('fullscreenchange', () => {
    if (document.fullscreenElement) {
        fullscreenButton.textContent = 'Exit full screen';
    } else {
        fullscreenButton.textContent = 'Full screen';
    }

    setTimeout(resizeRenderer, 150);
});

const defaultCameraPosition = new THREE.Vector3(0, 1.1, 2.6);
const defaultControlsTarget = new THREE.Vector3(0, 0.15, 0);

function moveCamera(x, y, z) {
    camera.position.x += x;
    camera.position.y += y;
    camera.position.z += z;

    controls.target.x += x;
    controls.target.y += y;
    controls.target.z += z;

    controls.update();
}

document.addEventListener('keydown', (event) => {
    const normalStep = 0.12;
    const fastStep = 0.28;
    const step = event.shiftKey ? fastStep : normalStep;

    if (event.key === 'ArrowUp') {
        event.preventDefault();
        moveCamera(0, step, 0);
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        moveCamera(0, -step, 0);
    }

    if (event.key === 'ArrowLeft') {
        event.preventDefault();
        moveCamera(-step, 0, 0);
    }

    if (event.key === 'ArrowRight') {
        event.preventDefault();
        moveCamera(step, 0, 0);
    }

    // W = move closer
    if (event.key.toLowerCase() === 'w') {
        event.preventDefault();
        moveCamera(0, 0, -step);
    }

    // S = move further away
    if (event.key.toLowerCase() === 's') {
        event.preventDefault();
        moveCamera(0, 0, step);
    }

    // R = reset camera
    if (event.key.toLowerCase() === 'r') {
        event.preventDefault();

        camera.position.copy(defaultCameraPosition);
        controls.target.copy(defaultControlsTarget);
        controls.update();
    }
});

// Rotate laptop itself with mouse drag
canvas.addEventListener('pointerdown', (event) => {
    isDragging = true;
    suppressClick = false;
    previousMouseX = event.clientX;
    previousMouseY = event.clientY;
    canvas.setPointerCapture(event.pointerId);
});

canvas.addEventListener('pointermove', (event) => {
    if (!isDragging || !laptop) {
        return;
    }

    const deltaX = event.clientX - previousMouseX;
    const deltaY = event.clientY - previousMouseY;

    if (Math.abs(deltaX) + Math.abs(deltaY) > 4) {
        suppressClick = true;
    }

    if (!event.shiftKey) {
        laptop.rotation.y += deltaX * 0.008;
        laptop.rotation.x += deltaY * 0.008;
    }

    if (event.shiftKey) {
        laptop.rotation.z += deltaX * 0.008;
    }

    previousMouseX = event.clientX;
    previousMouseY = event.clientY;
});

canvas.addEventListener('pointerup', (event) => {
    isDragging = false;
    canvas.releasePointerCapture(event.pointerId);
});

canvas.addEventListener('pointerleave', () => {
    isDragging = false;
});

canvas.addEventListener('contextmenu', (event) => {
    event.preventDefault();
});

// Screen interaction through raycasting
const raycaster = new THREE.Raycaster();
const pointer = new THREE.Vector2();

function getScreenClickPosition(event) {
    if (!screenMesh || !laptopScreenUI) {
        return null;
    }

    const rect = renderer.domElement.getBoundingClientRect();

    pointer.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    pointer.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

    raycaster.setFromCamera(pointer, camera);

    const intersects = raycaster.intersectObject(screenMesh, true);

    if (intersects.length === 0 || !intersects[0].uv) {
        return null;
    }

    const uv = intersects[0].uv;

    return {
        x: uv.x * screenCanvas.width,
        y: (1 - uv.y) * screenCanvas.height,
    };
}

canvas.addEventListener('click', (event) => {
    if (suppressClick) {
        suppressClick = false;
        return;
    }

    const screenPosition = getScreenClickPosition(event);

    if (!screenPosition || !laptopScreenUI) {
        return;
    }

    laptopScreenUI.handleClick(screenPosition.x, screenPosition.y);
});

function resizeRenderer() {
    let width = canvas.clientWidth;
    let height = canvas.clientHeight;

    if (document.fullscreenElement === fullscreenWrapper) {
        width = window.innerWidth;
        height = window.innerHeight;
    }

    camera.aspect = width / height;
    camera.updateProjectionMatrix();

    renderer.setSize(width, height, false);
}

window.addEventListener('resize', resizeRenderer);

function animate() {
    requestAnimationFrame(animate);

    if (screenPart) {
        screenOpenProgress += (targetScreenOpenProgress - screenOpenProgress) * 0.06;

        screenPart.rotation.x = THREE.MathUtils.lerp(
            screenClosedAngle,
            screenOpenAngle,
            screenOpenProgress
        );
    }

    controls.update();
    renderer.render(scene, camera);
}

animate();