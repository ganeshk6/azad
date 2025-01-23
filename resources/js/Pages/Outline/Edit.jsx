import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import SignatureCanvas from 'react-signature-canvas'

const Edit = ({ dictation }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [imageFile, setImageFile] = useState(null);
    const [getDictation, setDictation] = useState(dictation);
    const [notes, setNotes] = useState(dictation.notes || []); 

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setImageFile(file);

            const reader = new FileReader();
            reader.onload = (event) => {
                setDictation({ ...getDictation, image: event.target.result });
            };
            reader.readAsDataURL(file);
        }
    };
    
    const handleFileChange = (e) => {
        if (e.target.files && e.target.files[0]) {
            setImageFile(e.target.files[0]);
        }
    };

    const handleAddNote = () => {
        setNotes([...notes, { id: Date.now(), notes: '' }]);
    };

    const handleNoteChange = (index, value) => {
        const updatedNotes = [...notes];
        updatedNotes[index].notes = value;
        setNotes(updatedNotes);
    };

    const handleDeleteNote = (index) => {
        const updatedNotes = notes.filter((_, i) => i !== index);
        setNotes(updatedNotes);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        
        try {
            const formData = new FormData();
            formData.append('sentence', getDictation.sentence);
            formData.append('notes', JSON.stringify(notes)); 
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
                                <label htmlFor="wordSign" className="form-label">Word Signature</label>
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
                            <div className="bg-white shadow-md rounded-lg p-6 relative border-2 mt-3">
                                <div className='d-flex justify-between mb-3 align-items-center'>
                                    <label className="form-label h5 fw-bold">Search By</label>
                                    <button
                                        type="button"
                                        className="p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        onClick={handleAddNote}
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-plus-lg text-white me-2" viewBox="0 0 16 16">
                                            <path fillRule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                        </svg>
                                        Add More
                                    </button>
                                </div>
                                {notes.map((note, index) => (
                                    <div key={index} className="d-flex align-items-center mb-2">
                                        <input
                                            type="text"
                                            className="form-control rounded-1 me-2"
                                            placeholder="Enter note"
                                            value={note.notes} // Use note.notes instead of the entire note object
                                            onChange={(e) => handleNoteChange(index, e.target.value)}
                                        />
                                        <button
                                            type="button"
                                            className="btn btn-danger rounded-0"
                                            style={{ padding: "12px" }}
                                            onClick={() => handleDeleteNote(index)}
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-trash3" viewBox="0 0 16 16">
                                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                            </svg>
                                        </button>
                                    </div>
                                ))}
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
