import React, { useState, useRef, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import SignatureCanvas from 'react-signature-canvas'
import { Head } from '@inertiajs/react';

const Edit = ({ phrasesData }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [getPhrase, setPhrase] = useState(phrasesData);
    const [wordSection, setWordSections] = useState(phrasesData.wordSections);
    const [imageFile, setImageFile] = useState(null);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setImageFile(file);

            const reader = new FileReader();
            reader.onload = (event) => {
                setPhrase({ ...getPhrase, sign: event.target.result });
            };
            reader.readAsDataURL(file);
        }
    };

    const signatureRefs = useRef({});

    const handleAddSection = () => {
        setWordSections([...wordSection, { id: Date.now(), word: '', description: '', signature: null }]);
    };

    
    const handleRemoveSection = (id) => {
        setWordSections((prevSections) => prevSections.filter((section) => section.id !== id));
        delete signatureRefs.current[id]; 
    };
    
    const handleChange = (id, key, value) => {
        setWordSections(
            wordSection.map((section) => (section.id === id ? { ...section, [key]: value } : section))
        );
    };

    const handleSaveSignature = (id) => {
        const signatureRef = signatureRefs.current[id];
        if (!signatureRef) {
            console.error(`Signature ref missing for ID: ${id}`);
            return;
        }
    
        const signatureImage = signatureRef.isEmpty()
            ? wordSection.find((section) => section.id === id)?.signature 
            : signatureRef.getTrimmedCanvas().toDataURL('image/png'); 
    
        const updatedSections = wordSection.map((section) => 
            section.id === id && section.signature !== signatureImage 
            ? { ...section, signature: signatureImage }
            : section
        );
    
        if (JSON.stringify(updatedSections) !== JSON.stringify(wordSection)) {
            setWordSections(updatedSections);
        }
    };
    

    const handleClearSignature = (id) => {
        const signatureRef = signatureRefs.current[id];
        if (signatureRef) {
            signatureRef.clear();
        }
    };
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
    
        try {
            // Create a FormData object to handle the file upload
            const formData = new FormData();
            
            // Append regular data to FormData
            formData.append('letter', getPhrase.letter);
            if (wordSection && wordSection.length > 0) {
                formData.append('wordSections', JSON.stringify(wordSection));
            }
            if (imageFile) {
                formData.append('sign', imageFile); 
            }
    
            const response = await axios.post(route('countries-edit', getPhrase.id), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
    
        } catch (error) {
            console.error('Error updating phrases:', error);
            alert('Failed to update phrases. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title='Edit Phrases' />

            <div>
                <form onSubmit={handleSubmit}>
                    <div className="row">
                        <div className="col-10 bg-white shadow-md rounded-lg p-6 relative">
                            <div className="mb-3">
                                <label htmlFor="wordTitle" className="form-label">Phrases Letter Name</label>
                                <input
                                    id="wordTitle"
                                    type="text"
                                    required
                                    className="form-control rounded-1"
                                    placeholder="Enter word title"
                                    defaultValue={getPhrase.letter}
                                    onChange={(e) => setPhrase({ ...getPhrase, letter: e.target.value })}
                                />
                            </div>
                            <div className="bg-white shadow-md rounded-lg p-6 relative border-2 mt-3 mb-3">
                                <label htmlFor="wordSign" className="form-label">Word Signature</label>
                                {getPhrase.sign && (
                                    <img
                                        src={getPhrase.sign}
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
                                className={`p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center ${isLoading ? 'disabled' : ''}`}
                                disabled={isLoading}
                            >
                                {isLoading ? (
                                    <>
                                        <span role="status">Saving...</span>
                                        <span className="spinner-grow spinner-grow-sm ms-2" aria-hidden="true"></span>
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
