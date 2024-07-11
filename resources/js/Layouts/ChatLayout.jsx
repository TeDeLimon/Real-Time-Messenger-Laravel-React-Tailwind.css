import { usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";

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
                    return b.last_message_date.localCompare(a.last_message_date);
                } else if (a.last_message_date) {
                    return -1;
                } else if (b.last_message_date) {
                    return 1;
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
        <>{children}</>
    )
}

export default ChatLayout;