/**
 * Biometric Authentication Helper
 * معالج المصادقة البيومترية (الوجه والبصمة)
 */

class Biometrics {
    constructor() {
        this.faceApiUrl = 'https://loving-light-production-c248.up.railway.app';
        this.stream = null;
    }

    /**
     * Start Camera Stream
     */
    async startCamera(videoElement) {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 640, height: 480 } 
            });
            videoElement.srcObject = this.stream;
            return true;
        } catch (error) {
            console.error('Camera Access Denied:', error);
            return false;
        }
    }

    /**
     * Stop Camera Stream
     */
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
    }

    /**
     * Capture Frame from Video
     */
    captureFrame(videoElement) {
        const canvas = document.createElement('canvas');
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.8).split(',')[1]; // Base64 without prefix
    }

    /**
     * Capture a sequence of frames
     */
    async captureSequence(videoElement, count = 5, delay = 200) {
        const frames = [];
        for (let i = 0; i < count; i++) {
            frames.push(this.captureFrame(videoElement));
            await new Promise(resolve => setTimeout(resolve, delay));
        }
        return frames;
    }

    /**
     * Enroll Face - Get embedding from Python API
     */
    async enrollFace(images) {
        try {
            const response = await fetch(`${this.faceApiUrl}/enroll`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ images })
            });
            return await response.json();
        } catch (error) {
            console.error('Enroll Error:', error);
            return { status: 'error', message: 'تعذر الاتصال بخادم بصمة الوجه' };
        }
    }

    /**
     * Verify Face - Match against reference embedding
     */
    async verifyFace(images, referenceEmbedding) {
        try {
            const response = await fetch(`${this.faceApiUrl}/verify`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    images, 
                    reference_embedding: referenceEmbedding 
                })
            });
            return await response.json();
        } catch (error) {
            console.error('Verify Error:', error);
            return { status: 'denied', reason: 'connection_error', message: 'تعذر الاتصال بخادم التحقق' };
        }
    }

    /**
     * Fingerprint (WebAuthn) - Simplified for student project
     * Note: Proper WebAuthn requires server-side validation.
     * We will use the browser API to "Register" and "Verify".
     */
    async registerFingerprint(username) {
        if (!window.PublicKeyCredential) {
            return { status: 'error', message: 'بصمة الإصبع غير مدعومة في هذا المتصفح' };
        }

        // Basic WebAuthn creation options
        const challenge = new Uint8Array(32);
        window.crypto.getRandomValues(challenge);

        const options = {
            publicKey: {
                challenge: challenge,
                rp: { name: "TaskManager" },
                user: {
                    id: new Uint8Array(16), // Simplified
                    name: username,
                    displayName: username
                },
                pubKeyCredParams: [{ alg: -7, type: "public-key" }],
                authenticatorSelection: { authenticatorAttachment: "platform" },
                timeout: 60000
            }
        };

        try {
            const credential = await navigator.credentials.create(options);
            // In a real app, we send credential.response.attestationObject to server
            // Here we'll store a mock unique ID representing the fingerprint
            return { 
                status: 'success', 
                id: credential.id,
                message: 'تم تسجيل بصمة الإصبع بنجاح'
            };
        } catch (error) {
            console.error('Fingerprint Error:', error);
            return { status: 'error', message: 'فشل تسجيل البصمة: ' + error.message };
        }
    }

    async authenticateFingerprint() {
        if (!window.PublicKeyCredential) {
            return { status: 'error', message: 'بصمة الإصبع غير مدعومة' };
        }

        const challenge = new Uint8Array(32);
        window.crypto.getRandomValues(challenge);

        const options = {
            publicKey: {
                challenge: challenge,
                timeout: 60000,
                allowCredentials: [], // Empty means any registered credential on this device
                userVerification: "required"
            }
        };

        try {
            const assertion = await navigator.credentials.get(options);
            return { 
                status: 'success', 
                id: assertion.id,
                message: 'تم التحقق من البصمة'
            };
        } catch (error) {
            console.error('Fingerprint Auth Error:', error);
            return { status: 'error', message: 'فشل التحقق من البصمة' };
        }
    }
}

// Global instance
const biometrics = new Biometrics();
