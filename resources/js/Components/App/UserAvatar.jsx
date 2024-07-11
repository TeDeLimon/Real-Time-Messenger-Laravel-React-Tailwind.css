const UserAvatar = ({ user, online }) => {

    return (
        <div className="flex items-center">
            <img src={user.avatar} alt={user.name} className="w-10 h-10 rounded-full" />
            <div className={`w-3 h-3 bg-${online ? 'green' : 'red'}-500 rounded-full ml-2`}></div>
        </div>
    )
}

export default UserAvatar;