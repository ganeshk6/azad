import React, { useState, useRef, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
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
                setPhrase({ ...getPhrase, image: event.target.result });
            };
            reader.readAsDataURL(file);
        }
    };

    const signatureRefs = useRef({});

    const handleAddSection = () => {
        setWordSections([...wordSection, { id: Date.now(), word: '', description: '', signature: '' }]);
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

    const handleFileUpload = (file, callback) => {
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => callback(event.target.result);
            reader.readAsDataURL(file);
        }
    };

    const handleSubsectionImageChange = (id, e) => {
        const file = e.target.files[0];
        if (file) {
            handleFileUpload(file, (signature) => {
                setWordSections((sections) =>
                    sections.map((section) =>
                        section.id === id ? { ...section, signature } : section
                    )
                );
            });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
    
        try {
            const formData = new FormData();
            formData.append("sentence", getPhrase.sentence);
            formData.append("description", getPhrase.description);
            if (imageFile) {
                formData.append('image', imageFile);  
            }
            wordSection.forEach((section, index) => {
                formData.append(`wordSections[${index}][id]`, section.id);
                formData.append(`wordSections[${index}][word]`, section.word);
                formData.append(`wordSections[${index}][description]`, section.description);
                if (section.signature instanceof File) {
                    formData.append(`wordSections[${index}][signature]`, section.signature);
                } else if (section.signature && typeof section.signature === "string") {
                    formData.append(`wordSections[${index}][signatureUrl]`, section.signature);
                }
            });
    
            await axios.post(route("rulesOutlines-edit", getPhrase.id), formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            });
    
        } catch (error) {
            console.error("Error updating phrases:", error);
            alert("Failed to update phrases. Please try again.");
        } finally {
            setIsLoading(false);
        }
    };
    
    return (
        <AuthenticatedLayout>
            <Head title='Edit Outlines' />

            <div>
                <form onSubmit={handleSubmit}>
                    <div className="row">
                        <div className="col-10 bg-white shadow-md rounded-lg p-6 relative">
                            <div className="mb-3">
                                <label htmlFor="wordTitle" className="form-label">Rule Name</label>
                                <input
                                    id="wordTitle"
                                    type="text"
                                    required
                                    className="form-control rounded-1"
                                    placeholder="Enter word title"
                                    defaultValue={getPhrase.sentence}
                                    onChange={(e) => setPhrase({ ...getPhrase, sentence: e.target.value })}
                                />
                            </div>
                            {/* <div className="mb-3">
                                <label htmlFor="wordTitle" className="form-label">Description</label>
                                <CKEditor
                                    editor={ClassicEditor}
                                    data={getPhrase.description}
                                    config={{ licenseKey: 'GPL' }}
                                    onChange={(event, editor) => {
                                        const data = editor.getData();
                                        setPhrase({...getPhrase, description: data});
                                    }}
                                />
                            </div> */}
                            <div className="bg-white shadow-md rounded-lg p-6 relative border-2 mt-3 mb-3">
                                <label htmlFor="wordSign" className="form-label">Word Signature</label>
                                {getPhrase.image && (
                                    <img
                                        src={getPhrase.image}
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
                            <div>
                                <div className='d-flex justify-between mb-3 align-items-center'>
                                    <label className="form-label h5 fw-bold">Type of Rule</label>
                                    <button
                                        type="button"
                                        className="p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        onClick={handleAddSection}
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-plus-lg text-white me-2" viewBox="0 0 16 16">
                                            <path fillRule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                        </svg>
                                        Add More Word
                                    </button>
                                </div>
                                <div className="accordion" id="accordionExample">
                                    {wordSection.map((section, index) => (
                                        <div className="accordion-item" key={section.id}>
                                            <h2 className="accordion-header d-flex" id={`heading-${section.id}`}>
                                                <button
                                                    className="accordion-button"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target={`#collapse-${section.id}`}
                                                    aria-expanded="false"
                                                    aria-controls={`collapse-${section.id}`}
                                                >
                                                    {section.word ? section.word : 'Word Section'} {index + 1}
                                                </button>
                                                <button
                                                    type="button"
                                                    className="btn btn-danger rounded-0"
                                                    onClick={() => handleRemoveSection(section.id)}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-trash3" viewBox="0 0 16 16">
                                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                    </svg>
                                                </button>
                                            </h2>
                                            <div
                                                id={`collapse-${section.id}`}
                                                className="accordion-collapse collapse hide"
                                                aria-labelledby={`heading-${section.id}`}
                                                data-bs-parent="#accordionExample"
                                            >
                                                <div className="accordion-body">
                                                    <div className="mb-3">
                                                        <label htmlFor={`letter-${section.id}`} className="form-label">
                                                            Word of Letter
                                                        </label>
                                                        <input
                                                            id={`letter-${section.id}`}
                                                            type="text"
                                                            className="form-control rounded-1"
                                                            placeholder="Enter word title"
                                                            value={section.word}
                                                            onChange={(e) => handleChange(section.id, 'word', e.target.value)}
                                                        />
                                                    </div>
                                                    <div className="mb-3">
                                                        <label htmlFor={`description-${section.id}`} className="form-label">
                                                            Description of Word
                                                        </label>
                                                        <CKEditor
                                                            editor={ClassicEditor}
                                                            data={section.description}
                                                            config={{ licenseKey: 'GPL' }}
                                                            onChange={(event, editor) => {
                                                                const data = editor.getData();
                                                                handleChange(section.id, 'description', data);
                                                            }}
                                                        />
                                                    </div>
                                                    <div className="mb-3">
                                                        <label htmlFor={`signature-${section.id}`} className="form-label">
                                                            Word Signature
                                                        </label>
                                                        {section.signature && (
                                                            <img
                                                            src={typeof section.signature === "string" ? section.signature : URL.createObjectURL(section.signature)}
                                                            alt="Preview"
                                                            className="my-3 w-50 h-100"
                                                            style={{ width: "100px", height: "100px", objectFit: "cover" }}
                                                        />
                                                        )}
                                                        <input
                                                            id={`signature-${section.id}`}
                                                            type="file"
                                                            className="form-control rounded-1 border-2 p-2"
                                                            accept="image/*"
                                                            onChange={(e) => handleSubsectionImageChange(section.id, e)}
                                                        />

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
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

