import React, { useState } from 'react';
import axios from 'axios';
import './SketchGenerator.css';

function SketchGenerator() {
  const [prompt, setPrompt] = useState("");
  const [image, setImage] = useState(null);
  const [result, setResult] = useState("");
  const [loading, setLoading] = useState(false);

  // 🔹 Replace this with the ngrok URL printed in Colab
  const BACKEND_URL = "https://f4686bd58914.ngrok-free.app/generate";

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);

    const formData = new FormData();
    formData.append("prompt", prompt);
    if (image) {
      formData.append("image", image);
    }

    try {
      const res = await axios.post(BACKEND_URL, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      setResult(res.data.image_base64);
    } catch (err) {
      console.error(err);
      alert("❌ Error generating image. Check backend logs.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="sketch-generator-container">
      <div className="sketch-generator-header">
        <h2>🎨 AI Image Generator</h2>
        <p>Create beautiful images from text descriptions or transform your sketches</p>
      </div>
      
      <form onSubmit={handleSubmit} className="sketch-generator-form">
        <div className="input-group">
          <label htmlFor="prompt-input">Enter your prompt</label>
          <input
            id="prompt-input"
            type="text"
            placeholder="Describe what you want to generate..."
            value={prompt}
            onChange={(e) => setPrompt(e.target.value)}
            required
          />
        </div>
        
        <div className="file-input-container">
          <label htmlFor="image-input">Upload reference image (optional)</label>
          <input
            id="image-input"
            type="file"
            accept="image/*"
            onChange={(e) => setImage(e.target.files[0])}
          />
          <p>Upload an image for sketch-to-image, or leave empty for text-to-image generation</p>
        </div>
        
        <button 
          type="submit" 
          className="generate-btn" 
          disabled={loading}
        >
          {loading ? "⏳ Generating..." : "Generate Sketch"}
        </button>
      </form>

      {loading && (
        <div className="loading-spinner">
          <div>Creating your masterpiece...</div>
        </div>
      )}

      {result && (
        <div className="result-container">
          <h3>Your Generated Image</h3>
          <img
            src={`data:image/png;base64,${result}`}
            alt="Generated artwork"
            className="result-image"
          />
        </div>
      )}
    </div>
  );
}

export default SketchGenerator;