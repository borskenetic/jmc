(function () {
  const panel = document.getElementById('faceEnrollPanel');
  if (!panel) return;

  const video = document.getElementById('faceEnrollVideo');
  const canvas = document.getElementById('faceEnrollCanvas');
  const statusEl = document.getElementById('faceEnrollStatus');
  const captureBtn = document.getElementById('faceEnrollCapture');
  const removeBtn = document.getElementById('faceEnrollRemove');
  const storeUrl = panel.dataset.storeUrl;
  const destroyUrl = panel.dataset.destroyUrl;
  const csrf = panel.dataset.csrf;

  let stream = null;
  let running = false;

  function setStatus(msg, isError) {
    if (!statusEl) return;
    statusEl.textContent = msg;
    statusEl.classList.toggle('text-danger', !!isError);
    statusEl.classList.toggle('text-success', !isError && msg.includes('success'));
  }

  async function startCamera() {
    stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
      audio: false,
    });
    video.srcObject = stream;
    await video.play();
  }

  async function init() {
    try {
      await startCamera();
      await FaceApiHelper.loadModels(statusEl);
      running = true;
      setStatus('Position face in frame, then click Capture.');
      requestAnimationFrame(previewLoop);
    } catch (e) {
      setStatus(e.message || 'Camera or models unavailable.', true);
      captureBtn.disabled = true;
    }
  }

  async function previewLoop() {
    if (!running) return;
    try {
      const detection = await faceapi
        .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks(true);
      FaceApiHelper.drawDetection(canvas, video, detection);
    } catch (e) { /* ignore frame errors */ }
    requestAnimationFrame(previewLoop);
  }

  captureBtn?.addEventListener('click', async () => {
    captureBtn.disabled = true;
    setStatus('Capturing…');
    try {
      const descriptor = await FaceApiHelper.getDescriptorFromVideo(video);
      if (!descriptor) {
        setStatus('No face detected. Try again.', true);
        captureBtn.disabled = false;
        return;
      }
      const res = await fetch(storeUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ descriptor }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Enrollment failed');
      setStatus('Face enrolled successfully.');
      removeBtn?.removeAttribute('hidden');
      panel.dataset.enrolled = '1';
    } catch (e) {
      setStatus(e.message || 'Enrollment failed.', true);
    }
    captureBtn.disabled = false;
  });

  removeBtn?.addEventListener('click', async () => {
    if (!confirm('Remove face enrollment for this student?')) return;
    removeBtn.disabled = true;
    try {
      const res = await fetch(destroyUrl, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
      });
      if (!res.ok) throw new Error('Could not remove enrollment');
      setStatus('Face enrollment removed.');
      removeBtn.setAttribute('hidden', '');
      panel.dataset.enrolled = '0';
    } catch (e) {
      setStatus(e.message, true);
    }
    removeBtn.disabled = false;
  });

  init();
})();
