/**
 * SIGAP DOKUMEN Client-Side Uploader & Compressor
 * - Adaptive Canvas Image Compression
 * - Adaptive PDF Rendering Compression (PDF.js + pdf-lib)
 * - Async Upload via XMLHttpRequest (XHR) for progress tracking
 */

window.SigapDokumenUploader = {
    // 1. Engine Kompresi Gambar via Canvas
    compressImageAdaptive(file, compressionPercent, onProgress) {
        return new Promise((resolve) => {
            if (typeof onProgress === 'function') onProgress(50, 'Mengompres gambar...');

            let maxDim = 1600;
            let quality = 0.65;

            if (compressionPercent === '30') {
                maxDim = 1920;
                quality = 0.80;
            } else if (compressionPercent === '70') {
                maxDim = 1200;
                quality = 0.45;
            }

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    if (width > height && width > maxDim) {
                        height = Math.round((height * maxDim) / width);
                        width = maxDim;
                    } else if (height > maxDim) {
                        width = Math.round((width * maxDim) / height);
                        height = maxDim;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (typeof onProgress === 'function') onProgress(100, 'Gambar siap');
                        if (blob && blob.size < file.size) {
                            resolve(new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), { type: 'image/jpeg', lastModified: Date.now() }));
                        } else {
                            resolve(file);
                        }
                        canvas.width = 0;
                        canvas.height = 0;
                    }, 'image/jpeg', quality);
                };
                img.onerror = () => resolve(file);
            };
            reader.onerror = () => resolve(file);
        });
    },

    // 2. Engine Kompresi PDF via Render Gambar (PDF.js + PDFLib)
    async compressPdfAdaptive(file, compressionPercent, onProgress) {
        if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
            return file;
        }

        let scale = 1.0;
        let quality = 0.55;

        if (compressionPercent === '30') {
            scale = 1.2;
            quality = 0.75;
        } else if (compressionPercent === '70') {
            scale = 0.75;
            quality = 0.35;
        }

        try {
            if (typeof onProgress === 'function') onProgress(10, 'Membaca PDF...');
            const fileBuffer = await file.arrayBuffer();
            const loadingTask = pdfjsLib.getDocument({ data: fileBuffer });
            const pdfDoc = await loadingTask.promise;
            const numPages = pdfDoc.numPages;

            if (numPages === 0) return file;

            const newPdfDoc = await PDFLib.PDFDocument.create();

            for (let i = 1; i <= numPages; i++) {
                if (typeof onProgress === 'function') {
                    onProgress(Math.round(((i - 1) / numPages) * 100), `Mengompresi hal ${i} dari ${numPages}...`);
                }

                const page = await pdfDoc.getPage(i);
                const viewport = page.getViewport({ scale: scale });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({ canvasContext: context, viewport: viewport }).promise;

                const dataUrl = canvas.toDataURL('image/jpeg', quality);
                const imgBytes = this.dataURLtoUint8Array(dataUrl);

                const embeddedImg = await newPdfDoc.embedJpg(imgBytes);
                const newPage = newPdfDoc.addPage([viewport.width, viewport.height]);

                newPage.drawImage(embeddedImg, {
                    x: 0,
                    y: 0,
                    width: viewport.width,
                    height: viewport.height
                });

                canvas.width = 0;
                canvas.height = 0;
            }

            if (typeof onProgress === 'function') onProgress(95, 'Menyusun ulang hasil PDF...');

            const compressedPdfBytes = await newPdfDoc.save();
            const finalBlob = new Blob([compressedPdfBytes], { type: 'application/pdf' });

            if (typeof onProgress === 'function') onProgress(100, 'PDF siap');

            if (finalBlob.size < file.size) {
                return new File([finalBlob], file.name, { type: 'application/pdf', lastModified: Date.now() });
            }

            return file;
        } catch (err) {
            console.error('Kompresi PDF gagal:', err);
            return file; // Fallback ke file asli jika gagal
        }
    },

    dataURLtoUint8Array(dataurl) {
        const arr = dataurl.split(',');
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        return u8arr;
    },

    // 3. Upload Asinkron per-file dengan XHR
    uploadWithXHR(file, uploadUrl, csrfToken, onProgress) {
        return new Promise((resolve) => {
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', csrfToken);

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const progress = Math.round((e.loaded / e.total) * 100);
                    if (typeof onProgress === 'function') onProgress(progress);
                }
            });

            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            resolve({ success: res.success, temp_path: res.temp_path });
                        } catch (e) {
                            resolve({ success: false });
                        }
                    } else {
                        resolve({ success: false });
                    }
                }
            };

            xhr.onerror = () => resolve({ success: false });

            xhr.open('POST', uploadUrl, true);
            xhr.send(formData);
        });
    }
};