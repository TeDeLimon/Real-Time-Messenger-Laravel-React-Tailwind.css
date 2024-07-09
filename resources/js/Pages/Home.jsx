import ChatLayout from '@/Layouts/ChatLayout';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

function Home({ auth }) {
    return <>Message</>;
}

Home.layout = (page) => {
    return (
        <AuthenticatedLayout>
            <ChatLayout children={page} />
        </AuthenticatedLayout>
    );
}

export default Home;
