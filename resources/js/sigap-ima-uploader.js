/**
 * SIGAP IMA Client-Side Uploader & Compressor
 * - Canvas Image Compression (Max ~1600px, Quality ~75%)
 * - 1MB Chunked Upload via Fetch API (Kebal batasan post_max_size/upload_max_filesize 2MB)
 */

window.SigapImaUploader = {
    CHUNK_SIZE: 1024 * 1024, // 1MB per chunk

    // Kompresi Canvas untuk Gambar
    compressImage(file) {
        return new Promise((resolve) => {
            if (!file.type.startsWith('image/')) {
                return resolve(file);
            }

            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const MAX_WIDTH = 1600;
                    const MAX_HEIGHT = 1600;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height = Math.round((height * MAX_WIDTH) / width);
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width = Math.round((width * MAX_HEIGHT) / height);
                            height = MAX_HEIGHT;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(
                        (blob) => {
                            if (!blob) return resolve(file);
                            const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(compressedFile);
                        },
                        'image/jpeg',
                        0.75
                    );
                };
                img.onerror = () => resolve(file);
            };
            reader.onerror = () => resolve(file);
        });
    },

    // Upload per Chunk via Fetch API
    async uploadFileInChunks(file, onProgress, uploadUrl, csrfToken) {
        const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
        const totalChunks = Math.ceil(file.size / this.CHUNK_SIZE);

        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            const start = chunkIndex * this.CHUNK_SIZE;
            const end = Math.min(start + this.CHUNK_SIZE, file.size);
            const chunkBlob = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunkBlob);
            formData.append('file_id', fileId);
            formData.append('chunk_index', chunkIndex);
            formData.append('total_chunks', totalChunks);
            formData.append('original_name', file.name);

            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Upload chunk ${chunkIndex + 1}/${totalChunks} gagal.`);
            }

            const resData = await response.json();

            const progress = Math.round(((chunkIndex + 1) / totalChunks) * 100);
            if (typeof onProgress === 'function') {
                onProgress(progress, resData);
            }

            if (resData.completed) {
                return resData;
            }
        }
    }
};

// Registrasi Komponen Alpine
document.addEventListener('alpine:init', () => {
    Alpine.data('imaEvidenceItem', (indicatorId) => ({
        indicatorId: indicatorId,
        files: [],
        uploadUrl: '',
        csrfToken: '',

        init() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = meta ? meta.content : '';
            this.uploadUrl = window.SIGAP_IMA_UPLOAD_URL || '/sigap-ima/upload-chunk';
        },

        async handleFileInput(e) {
            const rawFiles = Array.from(e.target.files);
            if (!rawFiles.length) return;

            for (const file of rawFiles) {
                const item = {
                    id: 'doc_' + Date.now() + Math.random().toString(36).substring(2, 6),
                    name: file.name,
                    size: (file.size / 1024).toFixed(1) + ' KB',
                    progress: 0,
                    status: 'compressing', // 'compressing', 'uploading', 'success', 'error'
                    temp_path: '',
                    original_name: file.name
                };
                this.files.push(item);

                // Jalankan proses mandiri per-file
                this.processSingleFile(item, file);
            }
            e.target.value = '';
        },

        async processSingleFile(fileItem, rawFile) {
            try {
                // 1. Kompres gambar di canvas jika file adalah gambar
                const readyFile = await window.SigapImaUploader.compressImage(rawFile);
                fileItem.status = 'uploading';

                // 2. Upload chunk via fetch
                const result = await window.SigapImaUploader.uploadFileInChunks(
                    readyFile,
                    (progress) => {
                        fileItem.progress = progress;
                    },
                    this.uploadUrl,
                    this.csrfToken
                );

                if (result && result.completed) {
                    fileItem.status = 'success';
                    fileItem.temp_path = result.temp_path;
                    fileItem.original_name = result.original_name;
                } else {
                    fileItem.status = 'error';
                }
            } catch (err) {
                console.error(err);
                fileItem.status = 'error';
            }
        },

        removeFile(index) {
            this.files.splice(index, 1);
        },

        // Helper untuk submit JSON
        getUploadedPaths() {
            return this.files
                .filter(f => f.status === 'success' && f.temp_path)
                .map(f => ({
                    temp_path: f.temp_path,
                    original_name: f.original_name
                }));
        }
    }));
});