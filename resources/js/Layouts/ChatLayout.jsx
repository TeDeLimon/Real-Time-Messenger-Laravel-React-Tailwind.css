import { usePage } from "@inertiajs/react";
import { useEffect } from "react";

const ChatLayout = ({ children }) => {

    // Get the current page, which contains the props 

    const page = usePage();

    const conversations = page.props.conversations;

    // The Selected Conversation at the beginning is null

    const selectedConversation = page.props.selectedConversation;

    console.log('conversations :>> ', conversations);
    console.log('selectedConversation :>> ', selectedConversation);

    useEffect(() => {

        /*
        Join the Echo Channel for a specific channel:
            - here: Whenever I connect the channel, get the list of users in the channel
            - joining: Get the user who joined the channel
            - leaving: Get the user who left the channel
        */
        Echo.join('presence-online')
            .here((users) => {
                console.log('users :>> ', users);
            })
            .joining((user) => {
                console.log('user :>> ', user);
            })
            .leaving((user) => {
                console.log('user :>> ', user);
            })
            .error( (error) => {
                console.error('error :>> ', error);
            })
    }, [])

    return (
        <>{children}</>
    )
}

export default ChatLayout;