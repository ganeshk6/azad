import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import SignatureCanvas from 'react-signature-canvas'

const Edit = ({ dictation }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [getDictation, setDictation] = useState(dictation);
    const [signWord, setSign] = useState();
    const [newSign, setNewSign] = useState(dictation.image);

    const handleClear = () => {
        signWord.clear();
        setNewSign(null);
    };

    const handleSave = () => {
        setNewSign(signWord.getTrimmedCanvas().toDataURL('image/png'));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        const signatureImage = signWord && !signWord.isEmpty()
                ? signWord.getTrimmedCanvas().toDataURL('image/png')
                : newSign;
            setNewSign(signatureImage);

        try {
            const formData = new FormData();
            formData.append('sentence', getDictation.sentence);
            formData.append('link', getDictation.link);
            if (signatureImage) {
                formData.append('image', signatureImage);
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
                                {newSign && 
                                    <img src={`https://azadshorthand.com/admin/public/${newSign}`} alt="Signature" className="my-3" />

                                    }
                                <SignatureCanvas
                                    canvasProps={{ className: 'sigCanvas' }}
                                    ref={(data) => setSign(data)}
                                />
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary mt-3"
                                    onClick={handleClear}
                                >
                                    Clear sign
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-success mt-3 ms-2"
                                    onClick={handleSave}
                                >
                                    save Sign
                                </button>
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
