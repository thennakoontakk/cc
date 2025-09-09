import React, { useState } from "react";
import axios from "axios";

function App() {
  const [prompt, setPrompt] = useState("");
  const [image, setImage] = useState(null);
  const [result, setResult] = useState("");
  const [loading, setLoading] = useState(false);

  // 🔹 Replace this with the ngrok URL printed in Colab
  const BACKEND_URL = "https://c7512c26a16e.ngrok-free.app/generate";

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
    <div style={{ padding: 20 }}>
      <h2>🎨 AI Image Generator (Colab + React)</h2>
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          placeholder="Enter a prompt"
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
          required
          style={{ width: "400px", padding: "8px" }}
        />
        <br />
        <br />
        <input
          type="file"
          accept="image/*"
          onChange={(e) => setImage(e.target.files[0])}
        />
        <p style={{ fontSize: "12px", color: "#666" }}>
          (Upload image for sketch-to-image, leave empty for text-to-image)
        </p>
        <button type="submit" disabled={loading}>
          {loading ? "⏳ Generating..." : "Generate"}
        </button>
      </form>

      {result && (
        <div style={{ marginTop: "20px" }}>
          <h3>✅ Generated Image:</h3>
          <img
            src={`data:image/png;base64,${result}`}
            alt="Generated"
            style={{ maxWidth: "500px", border: "1px solid #ccc" }}
          />
        </div>
      )}
    </div>
  );
}

export default App;
