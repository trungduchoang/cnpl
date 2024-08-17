import React, { useState } from 'react';
import './Register.css'; 

const Register = () => {
    const defaultValues = {
        id: "AUVoLzJN54eoFv89O3Q8xwOkQGaOA_yc7hU9oFXCLmKsJomDlFK-30OtWu0zNjK3pW7YVZ5F69Vp",
        clientDataJson: "eyJjaGFsbGVuZ2UiOiI0OTJjMTE1NDA2NTQxMjg2YjE5MTE1NGM3NWFlM2UyNGY0N2RhNGQ2IiwidHlwZSI6IndlYmF1dGhuLmNyZWF0ZSIsIm9yaWdpbiI6Imh0dHBzOnBsYXRlLmlkIn0",
        attestationObject: "eyJhdHRTdG10Ijp7InNpZyI6IjExMTExMTExMTExMTExMTExIn0sImF1dGhEYXRhIjoiMjIyMjIifQ",
        projectId: "1111",
        cognito: 1
    };

    const [formData, setFormData] = useState(defaultValues);

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value,
        });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        const body = {
            id: formData.id,
            clientDataJson: formData.clientDataJson,
            attestationObject: formData.attestationObject,
            projectId: formData.projectId,
            cognito: parseInt(formData.cognito, 10)
        };

        try {
            const response = await fetch('https://fido-api.happygo24h.com/api/auth/signup/assertion/result', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(body),
            });

            if (response.ok) {
                const responseData = await response.json();
                console.log('Registration successful!');
                console.log('Received ID:', responseData.id);
                localStorage.setItem('userId', responseData.id);
                localStorage.setItem('userData', JSON.stringify(responseData));
                window.location.href = '/';
            } else {
                console.error('Failed to register');
            }
        } catch (error) {
            console.error('An error occurred:', error);
        }
    };

    return (
        <div className="register-container">
            <h2>Demo Register</h2>
            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="id">ID:</label>
                    <input
                        type="text"
                        id="id"
                        name="id"
                        value={formData.id}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="form-group">
                    <label htmlFor="clientDataJson">Client Data JSON:</label>
                    <input
                        type="text"
                        id="clientDataJson"
                        name="clientDataJson"
                        value={formData.clientDataJson}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="form-group">
                    <label htmlFor="attestationObject">Attestation Object:</label>
                    <input
                        type="text"
                        id="attestationObject"
                        name="attestationObject"
                        value={formData.attestationObject}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="form-group">
                    <label htmlFor="projectId">Project ID:</label>
                    <input
                        type="text"
                        id="projectId"
                        name="projectId"
                        value={formData.projectId}
                        onChange={handleChange}
                        required
                    />
                </div>
                <div className="form-group">
                    <label htmlFor="cognito">Cognito:</label>
                    <input
                        type="number"
                        id="cognito"
                        name="cognito"
                        value={formData.cognito}
                        onChange={handleChange}
                        required
                    />
                </div>
                <button type="submit">Register</button>
            </form>
        </div>
    );
};

export default Register;
