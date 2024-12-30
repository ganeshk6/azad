import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';

const Edit = ({ dictation }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [getDictation, setDictation] = useState(dictation);

    const handleFileChange = (e) => {
        if (e.target.files && e.target.files[0]) {
            setImageFile(e.target.files[0]);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const formData = new FormData();
            formData.append('sentence', getDictation.sentence);
            if (imageFile) {
                formData.append('image', imageFile);
            }

            await axios.post(route('outlines-edit', getDictation.id), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

        } catch (error) {
            console.error('Error updating outlines:', error);
            alert('Failed to update outlines. Please try again.');
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
                                <label htmlFor="wordTitle" className="form-label">Sentence</label>
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
                            <div className="bg-white shadow-md rounded-lg p-6 relative border-2 mt-3 mb-3">
                                <label htmlFor="wordImage" className="form-label">Image</label>
                                {getDictation.image && (
                                    <img src={`/${getDictation.image}`} alt="" className="my-3" />
                                )}
                                <input
                                    type="file"
                                    className="form-control border rounded p-2"
                                    accept="image/*"
                                    onChange={handleFileChange}
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
