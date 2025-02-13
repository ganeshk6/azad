import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import React, { useState, useEffect } from 'react';
import $ from 'jquery';
import 'datatables.net'; 
import axios from 'axios';
import NavLink from '@/Components/NavLink';

const Index = () => {
    const [letter, setLetter] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [words, setWords] = useState([]); 

    const languageId = localStorage.getItem('languageId');

    useEffect(() => {
        if (languageId) {
            fetchWords(languageId);
        } else {
            alert('Language ID not found.');
        }
    }, [languageId]); 
    
    useEffect(() => {
        if (words.length > 0) {
            if (!$.fn.DataTable.isDataTable('#table_id')) {
                $('#table_id').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                });
            }
        }
    }, [words]);
    
    

    const fetchWords = async (languageId) => {
        try {
            const response = await axios.get(route('festivals-getWords'), {
                params: {
                    languageId,
                },
            });

            if (response.data) {
                setWords(response.data);
            } else {
                alert('No words found for the selected language.');
            }
        } catch (error) {
            console.error('Error fetching words:', error);
            alert('Failed to fetch words. Please try again.');
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        const languageId = localStorage.getItem('languageId');
    
        if (!languageId) {
            alert('Language ID not found.');
            setIsLoading(false);
            return;
        }
    
        try {
            const response = await axios.post(route('festivals'), {
                letter,
                languageId,
            });
    
            if (response.status === 200) {
                setLetter(''); 
                setIsLoading(false); 
                const modalElement = document.getElementById('exampleModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
                
                const wordId = response.data.id;
                window.location.href = route('festivals-edit', { id: wordId });
                modalInstance.hide();
            }
        } catch (error) {
            console.error('Error saving word:', error);
            setIsLoading(false); 
            alert('Failed to save the word. Please try again.');
        }
    };

    const handleDelete = async (id) => {
        if (window.confirm('Are you sure you want to delete this word?')) {
            setIsLoading(true);
            try {
                const response = await axios.delete(route('festivals-delete', { id }));
                if (response.status === 200) {
                    setWords((prevState) => prevState.filter((word) => word.id !== id));
                }
            } catch (error) {
                console.error('Error deleting word:', error);
                alert('Failed to delete word. Please try again.');
            } finally {
                setIsLoading(false);
            }
        }
    };
    
    return (
        <AuthenticatedLayout>
        <Head title="Phrases" />
            <div className="bg-white shadow-md rounded-lg p-6 relative">
                <button
                    type="button"
                    className="p-2 position-absolute top-2 end-2 rounded-0 bg-[green] text-[#fff] d-flex align-items-center"
                    data-bs-toggle="modal"
                    data-bs-target="#exampleModal"
                    >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-plus-lg text-white me-2" viewBox="0 0 16 16">
                        <path fillRule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                    </svg>
                    
                    Add Letter
                </button>

                <div className='mt-5'>
                    <table id="table_id" className="display">
                        <thead>
                            <tr>
                            <th>Title</th>
                            <th>Created at</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {words.length > 0 && words.map((word) => (
                                <tr key={word.id}>
                                    <td>{word.letter}</td>
                                    <td>{new Date(word.created_at).toLocaleDateString()}</td>
                                    <td>
                                    <button
                                            className="btn btn-warning btn-sm me-2"
                                        >
                                            <Link
                                                href={route('festivals-edit', { id: word.id })}
                                            >
                                                Edit
                                            </Link>
                                        </button>
                                        <button
                                            className="btn btn-danger btn-sm"
                                            onClick={() => handleDelete(word.id)} 
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

        {/* Bootstrap Modal */}
        <div
            className="modal fade"
            id="exampleModal"
            tabIndex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div className="modal-dialog">
            <div className="modal-content">
                <div className="modal-header">
                <h5 className="modal-title" id="exampleModalLabel">
                    Add Letter
                </h5>
                <button
                    type="button"
                    className="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                </div>
                <div className="modal-body">
                <form onSubmit={handleSubmit}>
                    <div className="mb-3">
                    <label htmlFor="letter" className="form-label">
                        Letter Name
                    </label>
                    <input
                        id="letter"
                        type="text"
                        value={letter}
                        onChange={(e) => setLetter(e.target.value)}
                        required
                        className="form-control rounded-1"
                        placeholder="Enter word title"
                    />
                    </div>
                    <div className="modal-footer text-center">
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
                                'Add Word'
                            )}
                        </button>
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
        </AuthenticatedLayout>
    );
};

export default Index;
