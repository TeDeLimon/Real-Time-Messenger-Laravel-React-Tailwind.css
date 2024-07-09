import { usePage } from "@inertiajs/react";

const ChatLayout = ({ children }) => {

    // Get the current page, which contains the props 

    const page = usePage();

    const conversations = page.props.conversations;

    // The Selected Conversation at the beginning is null

    const selectedConversation = page.props.selectedConversation;

    console.log('conversations :>> ', conversations);
    console.log('selectedConversation :>> ', selectedConversation);

    return (
        <>{children}</>
    )
}

export default ChatLayout;