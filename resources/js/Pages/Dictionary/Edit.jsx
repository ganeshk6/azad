import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import { Head } from '@inertiajs/react';

const Edit = ({ dictionary }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [getDictionary, setDictionary] = useState(dictionary);
    const [subDictionaries, setSubDictionaries] = useState(dictionary.sub_entries || []);

    const handleAddSubDictionary = () => {
        setSubDictionaries([
            ...subDictionaries,
            { title: '', image: null, child_entries: [] },
        ]);
    };

    // Remove a sub-dictionary
    const handleRemoveSubDictionary = (index) => {
        const updatedSubDictionaries = subDictionaries.filter((_, i) => i !== index);
        setSubDictionaries(updatedSubDictionaries);
    };

    // Handle changes for sub-dictionary fields
    const handleSubDictionaryChange = (index, key, value) => {
        const updatedSubDictionaries = subDictionaries.map((entry, i) => {
            if (i === index) {
                return {
                    ...entry,
                    [key]: key === 'image' ? value : value,
                };
            }
            return entry;
        });
        setSubDictionaries(updatedSubDictionaries);
    };
    

    // Add a new child entry to a sub-dictionary
    const handleAddChild = (index) => {
        const updatedSubDictionaries = subDictionaries.map((entry, i) =>
            i === index
                ? {
                      ...entry,
                      child_entries: [...entry.child_entries, { title: '', description: '' }],
                  }
                : entry
        );
        setSubDictionaries(updatedSubDictionaries);
    };

    // Remove a child entry from a sub-dictionary
    const handleRemoveChild = (subIndex, childIndex) => {
        const updatedSubDictionaries = subDictionaries.map((entry, i) =>
            i === subIndex
                ? {
                      ...entry,
                      child_entries: entry.child_entries.filter((_, j) => j !== childIndex),
                  }
                : entry
        );
        setSubDictionaries(updatedSubDictionaries);
    };

    // Handle changes for child entry fields
    const handleChildChange = (subIndex, childIndex, key, value) => {
        const updatedSubDictionaries = subDictionaries.map((entry, i) =>
            i === subIndex
                ? {
                      ...entry,
                      child_entries: entry.child_entries.map((child, j) =>
                          j === childIndex
                              ? {
                                    ...child,
                                    [key]: key === 'image' ? value : value,
                                }
                              : child
                      ),
                  }
                : entry
        );
        setSubDictionaries(updatedSubDictionaries);
    };    

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
    
        try {
            const formData = new FormData();
            formData.append('word', getDictionary.word);
    
            subDictionaries.forEach((subDict, index) => {
                formData.append(`sub_entries[${index}][title]`, subDict.title);
                if (subDict.image) {
                    formData.append(`sub_entries[${index}][image]`, subDict.image);
                }
    
                subDict.child_entries.forEach((child, childIndex) => {
                    formData.append(
                        `sub_entries[${index}][child_entries][${childIndex}][title]`,
                        child.title
                    );
                    formData.append(
                        `sub_entries[${index}][child_entries][${childIndex}][description]`,
                        child.description
                    );
                    if (child.image) {
                        formData.append(
                            `sub_entries[${index}][child_entries][${childIndex}][image]`,
                            child.image
                        );
                    }
                });
            });
    
            const response = await axios.post(
                route('dictionary-edit', getDictionary.id),
                formData,
                { headers: { 'Content-Type': 'multipart/form-data' } }
            );
    
            // Handle successful response
            console.log('Response:', response.data);
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
                            {/* Main Title */}
                            <div className="mb-3">
                                <label htmlFor="wordTitle" className="form-label">
                                    Dictionary Title
                                </label>
                                <input
                                    id="wordTitle"
                                    type="text"
                                    className="form-control rounded-1"
                                    placeholder="Enter word title"
                                    value={getDictionary.word}
                                    onChange={(e) =>
                                        setDictionary({ ...getDictionary, word: e.target.value })
                                    }
                                />
                            </div>

                            {/* Sub Dictionary Section */}
                            <div className="mt-4 shadow-md rounded-lg p-6 bg-white border">
                                <div className='d-flex justify-between mb-3 align-items-center'>
                                    <label className="form-label h5 fw-bold">Family Words</label>
                                    <button
                                        type="button"
                                        className="p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center"
                                        data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        onClick={handleAddSubDictionary}
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-plus-lg text-white me-2" viewBox="0 0 16 16">
                                            <path fillRule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                        </svg>
                                        Add More
                                    </button>
                                </div>
                                <div className="accordion" id="accordionExample">
                                    {subDictionaries.map((subDict, subIndex) => (
                                        <div className="accordion-item mb-3 border" key={subIndex}>
                                            <h2 className="accordion-header d-flex" id={`heading-${subIndex}`}>
                                                <button
                                                    className="accordion-button"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target={`#collapse-${subIndex}`}
                                                    aria-expanded="false"
                                                    aria-controls={`collapse-${subIndex}`}
                                                >
                                                    {subDict.title ? subDict.title : 'Word Section'}
                                                </button>
                                                <button
                                                    type="button"
                                                    className="btn btn-danger rounded-0"
                                                    onClick={() => handleRemoveSubDictionary(subIndex)}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-trash3" viewBox="0 0 16 16">
                                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                    </svg>
                                                </button>
                                            </h2>
                                            <div
                                                id={`collapse-${subIndex}`}
                                                className="accordion-collapse collapse hide"
                                                aria-labelledby={`heading-${subIndex}`}
                                                data-bs-parent="#accordionExample"
                                            >
                                                <div className="accordion-body">
                                                    <div className="d-flex justify-content-between align-items-center gap-2">
                                                        <div className='w-100'>
                                                            <label htmlFor="" className="form-label">Sub Dictonary Title</label>
                                                            <input
                                                                type="text"
                                                                placeholder="Sub Dictionary Title"
                                                                className="form-control mb-2"
                                                                value={subDict.title}
                                                                onChange={(e) =>
                                                                    handleSubDictionaryChange(
                                                                        subIndex,
                                                                        'title',
                                                                        e.target.value
                                                                    )
                                                                }
                                                            />
                                                        </div>
                                                    </div>
                                                    <label htmlFor="" className="form-label">Sub Dictonary Image</label>
                                                    {subDict.image && <img src={subDict.image} alt="Preview" className="img-thumbnail w-50" />}
                                                    <input
                                                        type="file"
                                                        accept="image/*"
                                                        className="form-control rounded-1 border-2 p-2"
                                                        onChange={(e) =>
                                                            handleSubDictionaryChange(
                                                                subIndex,
                                                                'image',
                                                                e.target.files[0]
                                                            )
                                                        }
                                                    />
                                                        {/* Child Section */}
                                                    <div className="mt-3 shadow rounded-lg p-6 bg-white border">
                                                        <div className='d-flex justify-between mb-3 align-items-center'>
                                                            <label className="form-label h5 fw-bold">Similar Words</label>
                                                            <button
                                                                type="button"
                                                                className="p-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal"
                                                                onClick={() => handleAddChild(subIndex)}
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-plus-lg text-white me-2" viewBox="0 0 16 16">
                                                                    <path fillRule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                                                                </svg>
                                                                Add Child
                                                            </button>
                                                        </div>
                                                        {subDict.child_entries.map((child, childIndex) => (
                                                            <div
                                                                key={childIndex}
                                                                className="p-2 border mb-2"
                                                            >
                                                                <div className="d-flex justify-content-between align-items-center gap-2">
                                                                    <div className='w-100'>
                                                                        <label htmlFor="wordTitle" className="form-label">Sub Child Title</label>
                                                                        <input
                                                                            type="text"
                                                                            placeholder="Child Title"
                                                                            className="form-control mb-2"
                                                                            value={child.title}
                                                                            onChange={(e) =>
                                                                                handleChildChange(
                                                                                    subIndex,
                                                                                    childIndex,
                                                                                    'title',
                                                                                    e.target.value
                                                                                )
                                                                            }
                                                                        />
                                                                    </div>
                                                                    <button
                                                                        type="button"
                                                                        className="btn btn-danger rounded-0 h-11 mt-0 mb-2"
                                                                        onClick={() =>
                                                                            handleRemoveChild(
                                                                                subIndex,
                                                                                childIndex
                                                                            )
                                                                        }
                                                                    >
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-trash3" viewBox="0 0 16 16">
                                                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                <label htmlFor="" className="form-label">Sub Child Image</label>
                                                                {child.image && <img src={child.image} alt="Preview" className="img-thumbnail mb-2 w-50" />}
                                                                <input
                                                                    type="file"
                                                                    accept="image/*"
                                                                    className="form-control rounded-1 border-2 p-2"
                                                                    onChange={(e) =>
                                                                        handleChildChange(
                                                                            subIndex,
                                                                            childIndex,
                                                                            'image',
                                                                            e.target.files[0]
                                                                        )
                                                                    }
                                                                />
                                                            </div>
                                                        ))}
                                                    </div>
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
                                className={`btn btn-success ${isLoading ? 'disabled' : ''}`}
                                disabled={isLoading}
                            >
                                {isLoading ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
};

export default Edit;
