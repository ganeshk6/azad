import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import SignatureCanvas from 'react-signature-canvas'

const Edit = ({ dictation }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [getDictation, setDictation] = useState(dictation);
    const [imageFile, setImageFile] = useState(null);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setImageFile(file);

            // Preview the new image immediately
            const reader = new FileReader();
            reader.onload = (event) => {
                setDictation({ ...getDictation, image: event.target.result });
            };
            reader.readAsDataURL(file);
        }
    };
    

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const formData = new FormData();
            formData.append('sentence', getDictation.sentence);
            formData.append('link', getDictation.link);
            if (imageFile) {
                formData.append('image', imageFile);
            }

            await axios.post(route('dictation-edit', getDictation.id), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

        } catch (error) {
            console.error('Error updating dictation:', error);
            alert('Failed to update dictation. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Edit Dictation" />

            <div>
                <form onSubmit={handleSubmit}>
                    <div className="row">
                        <div className="col-10 bg-white shadow-md rounded-lg p-6 relative">
                            <div className="mb-3">
                                <label htmlFor="wordTitle" className="form-label">Dictation Sentence</label>
                                <input
                                    id="wordTitle"
                                    type="text"
                                    required
                                    className="form-control rounded-1"
                                    placeholder="Enter sentence"
                                    defaultValue={getDictation.sentence}
                                    onChange={(e) =>
                                        setDictation({ ...getDictation, sentence: e.target.value })
                                    }
                                />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="wordLink" className="form-label">Link</label>
                                <input
                                    id="wordLink"
                                    type="text"
                                    required
                                    className="form-control rounded-1"
                                    placeholder="Enter link"
                                    defaultValue={getDictation.link}
                                    onChange={(e) =>
                                        setDictation({ ...getDictation, link: e.target.value })
                                    }
                                />
                            </div>
                            <div className="bg-white shadow-md rounded-lg p-6 relative border-2 mt-3 mb-3">
                                <label htmlFor="wordSign" className="form-label">Word Signature</label>
                                {/* {newSign && 
                                    <img src={`https://azadshorthand.com/admin/public${newSign}`} alt="Signature" className="my-3" />
                                    } */}
                                {getDictation.image && (
                                    <img
                                        src={getDictation.image}
                                        alt="Preview"
                                        className="my-3 w-50 h-100"
                                        style={{ width: '100px', height: '100px', objectFit: 'cover' }}
                                    />
                                )}
                                <input
                                    id="wordImage"
                                    type="file"
                                    className="form-control rounded-1 border-2 p-2"
                                    accept="image/*"
                                    onChange={handleImageChange}
                                />
                            </div>
                        </div>
                        <div className="col-2">
                            <button
                                type="submit"
                                className={`p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center ${
                                    isLoading ? 'disabled' : ''
                                }`}
                                disabled={isLoading}
                            >
                                {isLoading ? (
                                    <>
                                        <span role="status">Saving...</span>
                                        <span
                                            className="spinner-grow spinner-grow-sm ms-2"
                                            aria-hidden="true"
                                        ></span>
                                    </>
                                ) : (
                                    'Update'
                                )}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
};

export default Edit;
