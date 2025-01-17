import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import { Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function AuthenticatedLayout({ children }) {
    const user = usePage().props.auth.user;
    const [selectedLanguage, setSelectedLanguage] = useState('please select language');
    const [languageId, setLanguageId] = useState(localStorage.getItem('languageId'));

    useEffect(() => {
        // Set the selected language based on localStorage
        if (languageId) {
            const savedLanguage = localStorage.getItem('language');
            setSelectedLanguage(savedLanguage || 'please select language');
        }
    }, [languageId]);

    const handleLanguageChange = async (e) => {
        const language = e.target.value;

        if (language === 'please select language') {
            // Reset language if "please select language" is chosen
            setSelectedLanguage('please select language');
            setLanguageId(null);
            localStorage.removeItem('language');
            localStorage.removeItem('languageId');
            window.location.reload();
            return;
        }

        setSelectedLanguage(language);

        try {
            const response = await axios.post(route('language-set'), { language }, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });

            const { languageId: newLanguageId } = response.data;
            setLanguageId(newLanguageId);

            localStorage.setItem('language', language);
            localStorage.setItem('languageId', newLanguageId);

            // Reload or update the UI if necessary
            window.location.reload();
        } catch (error) {
            console.error('Error updating language:', error);
        }
    };

    return (
        <div className="min-h-screen bg-gray-100 flex w-full ">
            {/* Sidebar */}
            <aside className="w-64 bg-white border-r border-gray-200 flex-shrink-0 overflow-y-auto fixed h-screen">
                <div className="h-16 flex items-center justify-center border-b">
                    <Link href="/">
                        <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                    </Link>
                </div>
                <div className="space-y-4 p-4 d-flex flex-column">
                    <NavLink
                        href={route('dashboard')}
                        active={route().current('dashboard')}
                    >
                        Dashboard
                    </NavLink>
                    <NavLink
                        href={route('dictionary')}
                        active={route().current('dictionary') || route().current('dictionary-edit')}
                    >
                        Dictionary
                    </NavLink>
                    <NavLink
                        href={route('phrases')}
                        active={route().current('phrases') || route().current('phrases-edit')}
                    >
                        Phrases
                    </NavLink>
                    <NavLink
                        href={route('dictation')}
                        active={route().current('dictation') || route().current('dictation-edit')}
                    >
                        Dictation
                    </NavLink>
                    <NavLink
                        href={route('grammalogues')}
                        active={route().current('grammalogues') || route().current('grammalogues-edit')}
                    >
                        Grammalogues
                    </NavLink>
                    <NavLink
                        href={route('outlines')}
                        active={route().current('outlines') || route().current('outlines-edit')}
                    >
                        Basic Outlines
                    </NavLink>
                    <NavLink
                        href={route('rulesOutlines')}
                        active={route().current('rulesOutlines') || route().current('rulesOutlines-edit')}
                    >
                        Rules for Outlines
                    </NavLink>
                    <NavLink
                        href={route('contractions')}
                        active={route().current('contractions') || route().current('contractions-edit')}
                    >
                        Contractions
                    </NavLink>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col">
                {/* Top Navbar */}
                <nav
                    className="h-16 bg-white border-b border-gray-200 fixed flex items-center justify-end px-4 sm:px-6 lg:px-8 z-10"
                    style={{ marginLeft: '16rem', width: 'calc(100% - 16rem)' }}
                >
                    <div className="hidden sm:flex sm:items-center">
                        <select
                            className="border-1 bg-white rounded me-2"
                            value={selectedLanguage}
                            onChange={handleLanguageChange}
                        >
                            <option value="please select language">Please select language</option>
                            <option value="hindi">Hindi</option>
                            <option value="english">English</option>
                        </select>
                        <Dropdown>
                            <Dropdown.Trigger>
                                <span className="inline-flex rounded-md rounded">
                                    <button
                                        type="button"
                                        className="inline-flex border-2 rounded items-center bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 "
                                    >
                                        {user.name}
                                        <svg
                                            className="ml-2 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </Dropdown.Trigger>
                            <Dropdown.Content>
                                <Dropdown.Link href={route('profile.edit')}>
                                    Profile
                                </Dropdown.Link>
                                <Dropdown.Link
                                    href={route('logout')}
                                    method="post"
                                    as="button"
                                >
                                    Log Out
                                </Dropdown.Link>
                            </Dropdown.Content>
                        </Dropdown>
                    </div>
                    
                </nav>

                {/* Scrollable Main Content */}
                <main className="flex-1 overflow-y-auto mt-16 p-4 " style={{ marginLeft: '16rem', width: 'calc(100% - 16rem)' }}>
                    {children}
                </main>
            </div>
        </div>
    );
}
