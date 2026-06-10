/**
 * Shared face-api.js helpers (requires @vladmandic/face-api loaded globally).
 */
window.FaceApiHelper = (function () {
  let loaded = false;

  function modelUrl() {
    const panel = document.getElementById('faceEnrollPanel');
    if (panel?.dataset.faceModelCdn) {
      return panel.dataset.faceModelCdn;
    }
    if (document.body.dataset.faceModelCdn) {
      return document.body.dataset.faceModelCdn;
    }
    return 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model';
  }

  async function loadModels(statusEl) {
    if (loaded) return;
    if (typeof faceapi === 'undefined') {
      throw new Error('face-api library not loaded');
    }
    const url = modelUrl();
    const setStatus = (msg) => { if (statusEl) statusEl.textContent = msg; };
    setStatus('Loading face models…');
    await faceapi.nets.tinyFaceDetector.loadFromUri(url);
    await faceapi.nets.faceLandmark68TinyNet.loadFromUri(url);
    await faceapi.nets.faceRecognitionNet.loadFromUri(url);
    loaded = true;
    setStatus('Models ready');
  }

  async function getDescriptorFromVideo(video) {
    if (!video || video.readyState < 2) return null;
    const detection = await faceapi
      .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.5 }))
      .withFaceLandmarks(true)
      .withFaceDescriptor();
    if (!detection) return null;
    return Array.from(detection.descriptor);
  }

  function drawDetection(canvas, video, detection) {
    if (!canvas || !video) return;
    const displaySize = { width: video.videoWidth, height: video.videoHeight };
    faceapi.matchDimensions(canvas, displaySize);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (!detection) return;
    const resized = faceapi.resizeResults(detection, displaySize);
    faceapi.draw.drawDetections(canvas, resized);
    faceapi.draw.drawFaceLandmarks(canvas, resized);
  }

  return { loadModels, getDescriptorFromVideo, drawDetection, modelUrl };
})();
