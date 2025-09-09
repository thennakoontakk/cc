import React from 'react';
import './Input.css';

const Input = ({ type, placeholder, value, onChange, name, required = false, error }) => {
  return (
    <div className="input-container">
      <input
        type={type}
        placeholder={placeholder}
        value={value}
        onChange={onChange}
        name={name}
        required={required}
        className={`input-field ${error ? 'input-error' : ''}`}
      />
      {error && <div className="error-message">{error}</div>}
    </div>
  );
};

export default Input;