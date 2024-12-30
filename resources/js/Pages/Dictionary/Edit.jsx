import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import SignatureCanvas from 'react-signature-canvas';
import { Head } from '@inertiajs/react';

const Edit = ({ dictionary }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [signWord, setSign] = useState();
    const [newSign, setNewSign] = useState(dictionary.sign);
    const [getDictionary, setDictionary] = useState(dictionary);
    const [subEntries, setSubEntries] = useState(dictionary.sub_entries || []); // Initialize sub-entries

    const handleClear = () => {
        signWord.clear();
        setNewSign(null);
    };

    const handleSave = () => {
        setNewSign(signWord.getTrimmedCanvas().toDataURL('image/png'));
    };

    const handleAddSubEntry = () => {
        setSubEntries([...subEntries, { sub_word: '', sub_description: '' }]);
    };

    const handleRemoveSubEntry = (index) => {
        const updatedEntries = subEntries.filter((_, i) => i !== index);
        setSubEntries(updatedEntries);
    };

    const handleSubEntryChange = (index, key, value) => {
        const updatedEntries = subEntries.map((entry, i) =>
            i === index ? { ...entry, [key]: value } : entry
        );
        setSubEntries(updatedEntries);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const signatureImage = signWord && !signWord.isEmpty()
                ? signWord.getTrimmedCanvas().toDataURL('image/png')
                : newSign;
            setNewSign(signatureImage);

            const response = await axios.post(route('dictionary-edit', getDictionary.id), {
                word: getDictionary.word,
                description: getDictionary.description,
                sub_entries: subEntries,
                sign: signatureImage,
                clearSign: signWord && signWord.isEmpty() ? true : false,
            });
        } catch (error) {
            console.error('Error updating dictionary:', error);
            alert('Failed to update dictionary. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Edit Dictionary" />

            <div className="">
                <form onSubmit={handleSubmit}>
                    <div className="row">
                        <div className="col-10 bg-white shadow-md rounded-lg p-6 relative">
                            {/* Word Title */}
                            <div className="mb-3">
                                <label htmlFor="wordTitle" className="form-label">Title (Word Name)</label>
                                <input
                                    id="wordTitle"
                                    type="text"
                                    required
                                    className="form-control rounded-1"
                                    placeholder="Enter word title"
                                    defaultValue={getDictionary.word}
                                    onChange={(e) => setDictionary({ ...getDictionary, word: e.target.value })}
                                />
                            </div>

                            {/* Word Description */}
                            <label htmlFor="wordDescription" className="form-label">Description (Word)</label>
                            <CKEditor
                                editor={ClassicEditor}
                                data={getDictionary.description}
                                config={{ licenseKey: 'GPL' }}
                                onChange={(event, editor) => {
                                    const data = editor.getData();
                                    setDictionary({ ...getDictionary, description: data });
                                }}
                            />

                            {/* Signature */}
                            <div className="bg-white shadow-md rounded-lg p-6 relative border-2 mt-3 mb-3">
                                <label htmlFor="wordSign" className="form-label">Word Signature</label>
                                {newSign && <img src={newSign} alt="" className="my-3" />}
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
                                    Check Sign
                                </button>
                            </div>

                            {/* Sub Entries */}
                            <div className="mt-4">
                                <div className='d-flex justify-between mb-3 align-items-center'>
                                    <label className="form-label h5 fw-bold">Sub Word</label>
                                    <button
                                        type="button"
                                        className="p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        onClick={handleAddSubEntry}
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-plus-lg text-white me-2" viewBox="0 0 16 16">
                                            <path fillRule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                        </svg>
                                        Add More Sub Entry
                                    </button>
                                </div>
                                <div className="accordion" id="accordionExample">
                                    {subEntries.map((entry, index) => (
                                        <div key={index} className="accordion-item">
                                            <h2 className="accordion-header d-flex" id={`heading-${index}`}>
                                                <button
                                                    className="accordion-button"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target={`#collapse-${index}`}
                                                    aria-expanded="false"
                                                    aria-controls={`collapse-${index}`}
                                                >
                                                    {entry.sub_word ? entry.sub_word : `Sub Entry ${index + 1}` }
                                                </button>
                                                <button
                                                    type="button"
                                                    className="btn btn-danger rounded-0"
                                                    onClick={() => handleRemoveSubEntry(index)}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-trash3" viewBox="0 0 16 16">
                                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                    </svg>
                                                </button>
                                            </h2>
                                            <div
                                                id={`collapse-${index}`}
                                                className="accordion-collapse collapse hide"
                                                aria-labelledby={`heading-${index}`}
                                                data-bs-parent="#accordionExample"
                                            >
                                                <div className="accordion-body">
                                                    <input
                                                        type="text"
                                                        placeholder="Sub Word"
                                                        value={entry.sub_word}
                                                        onChange={(e) =>
                                                            handleSubEntryChange(index, 'sub_word', e.target.value)
                                                        }
                                                        className="form-control mb-2"
                                                    />
                                                    <CKEditor
                                                        editor={ClassicEditor}
                                                        data={entry.sub_description}
                                                        config={{ licenseKey: 'GPL' }}
                                                        onChange={(event, editor) => {
                                                            const data = editor.getData();
                                                            handleSubEntryChange(index, 'sub_description', data);
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Submit Button */}
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
                                    'Save Changes'
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
