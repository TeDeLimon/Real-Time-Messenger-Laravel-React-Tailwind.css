import { usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import { PencilSquareIcon } from "@heroicons/react/24/outline";
import TextInput from "@/Components/TextInput";
import ConversationItem from '@/Components/App/ConversationItem';

const ChatLayout = ({ children }) => {

    // Get the current page, which contains the props 

    const page = usePage();
    const conversations = page.props.conversations;
    // The Selected Conversation at the beginning is null
    const selectedConversation = page.props.selectedConversation;

    const [localConversations, setLocalConversations] = useState([]);
    const [sortedConversations, setSortedConversations] = useState([]);
    const [onlineUsers, setOnlineUsers] = useState({});

    //Simple function to check if a user is online
    const isUserOnline = (userId) => onlineUsers[userId];

    // This function will be called whenever the user types in the search bar to filter the conversations
    const onSearch = (ev) => {

        const search = ev.target.value.toLowerCase();

        setLocalConversations(
            conversations.filter((conversation) => conversation.name.toLowerCase().includes(search))
        )
    }

    useEffect(() => {

        setSortedConversations(

            localConversations.sort((a, b) => {

                // We want to show the blocked conversations at the end
                if (a.blocked_at && b.blocked_at) {
                    //If both are blocked, we sort them by the most recent
                    return a.blocked_at > b.blocked_at ? 1 : -1;
                } else if (a.blocked_at) {
                    return 1;
                } else if (b.blocked_at) {
                    return -1;
                }

                // We want to show most receant conversations at the top
                if (a.last_message_date && b.last_message_date) {

                    // LocalCompare will return 1 if a date is greater b date, -1 if a is than b, and 0 if they are equal
                    return b.last_message_date.localeCompare(a.last_message_date);
                } else if (a.last_message_date) {
                    return -1;
                } else if (b.last_message_date) {
                    return 1;
                } else {
                    return 0;
                }

            })
        );
    }, [localConversations]);

    // This useEffect will be activated only when the conversations change
    useEffect(() => {
        setLocalConversations(conversations);
    }, [conversations]);

    useEffect(() => {

        /*
            Join the Echo Channel for a specific channel:
                - here: Whenever I connect the channel, get the list of users in the channel
                - joining: Get the user who joined the channel
                - leaving: Get the user who left the channel
        */
        Echo.join('online')
            .here((users) => {

                const onlineUsersObj = Object.fromEntries(users.map((user) => [user.id, user]));

                // This is just in case there's already some online users
                setOnlineUsers((prevOnlineUsers) => {
                    return { ...prevOnlineUsers, ...onlineUsersObj }
                });
            })
            .joining((user) => {

                setOnlineUsers((prevOnlineUsers) => {
                    return {
                        ...prevOnlineUsers,
                        [user.id]: user
                    }
                })
            })
            .leaving((user) => {

                setOnlineUsers((prevOnlineUsers) => {
                    const updatedUsers = { ...prevOnlineUsers };
                    delete updatedUsers[user.id];
                    return updatedUsers;
                })
            })
            .error((error) => {
                console.error('error :>> ', error);
            })

        return () => {
            Echo.leave('online')
        }
    }, []);

    return (
        <>
            <section className='flex-1 w-full flex overflow-hidden'>
                <div
                    className={`transition-all w-full sm:w-[220px] md:w-[300px] bg-slate-800 flex flex-col overflow-hidden ${selectedConversation ? '-ml-[100%] sm:ml-0' : ''}`}
                >
                    <div className='flex items-center justify-between py-2 px-3 text-xl font-medium'>
                        {/* This is the title section */}
                        <h2>My Conversations</h2>
                        {/* This is the Group Creation section */}
                        {/* This are classes from DaisyUI, a Tailwind CSS Component Library */}
                        <div
                            className='tooltip tooltip-left'
                            data-tip='Create a new Group'
                        >
                            <button
                                className='text-gray-400 hover:text-gray-200'
                            >
                                <PencilSquareIcon className='w-4 h-4 inline-block ml-2' />
                            </button>
                        </div>
                        {/* This is the search section */}
                        <div className='p-3'>
                            <TextInput
                                onKeyUp={onSearch}
                                placeholder='Filter users and groups'
                                className='w-full'
                            />
                        </div>
                        <div className='flex-1 overflow-auto'>
                            {sortedConversations &&
                                sortedConversations.map((conversation) => (
                                    <ConversationItem
                                        key={`${conversation.is_group
                                            ? 'group_'
                                            : 'user_'
                                            }${conversation.id}`}
                                        conversation={conversation}
                                        online={!!isUserOnline(conversation.id)}
                                        selectedConversation={selectedConversation}
                                    />
                                ))}
                        </div>
                    </div>
                </div>
                <div className='flex-1 flex flex-col overflow-hidden'>
                    {children}
                </div>
            </section>
        </>
    )
}

export default ChatLayout;