import {h, render} from 'preact';
import {useContext, useEffect, useState} from "preact/hooks";
import {createContext} from 'preact/compat';
import classNames from "classnames";
import {
    MDBBtn,
    MDBModal,
    MDBModalBody,
    MDBModalContent,
    MDBModalDialog,
    MDBModalHeader,
    MDBModalTitle,
} from 'mdb-react-ui-kit';

const NotificationsContext = createContext();

// TODO: Use actual translations
const __     = (s) => s;
let isUnread = n => n.read_at === null;

function NotificationsContextProvider({children}) {
    const [notifications, setNotifications] = useState([]);
    const [isOpen, setIsOpen]               = useState(false);
    const [isLoaded, setIsLoaded]           = useState(false);

    console.log({isOpen});
    const toggleOpen = () => setIsOpen(open => !open);

    useEffect(() => {
        fetch("/notifications/latest") // TODO: Use the API instead, needs sufficient permissions on API routes
            .then(res => res.json())
            .then(setNotifications)
            .catch(e => console.error(e))
            .finally(() => setIsLoaded(true))

    }, []);

    const toggleRead = (id) => {
        const new_state = notifications.find(n => n.id === id).read_at ? null : "read";

        setNotifications(notifications
            .map(n => {
                if (n.id === id) return {...n, ...{read_at: new_state}};
                return n;
            })
        )
    }

    const markAllRead = () => {
        setNotifications(notifications
            .map(n => ({...n, ...{read_at: "all-read"}}))
        )
    }

    return <NotificationsContext.Provider
        value={{
            notifications,
            isOpen,
            setIsOpen,
            isLoaded,
            toggleOpen,
            toggleRead,
            markAllRead
        }}>
        {children}
    </NotificationsContext.Provider>
}

function NotificationsNavButton() {
    const {notifications, toggleOpen, isLoaded} = useContext(NotificationsContext);

    let numUnread = notifications.filter(isUnread).length;

    return (<a href="javascript:void(0)" // TODO: Convert into button since this doesn't link anywhere
               onClick={toggleOpen}
               className="nav-link notifications-board-toggle">
        <span className={classNames("notifications-bell fa-bell", {
            'fa': numUnread,
            'far': numUnread === 0
        })}></span>
        <span className="notifications-pill badge rounded-pill badge-notification"
              hidden={!(isLoaded && numUnread !== 0)}>
                    {numUnread}
                </span>
    </a>)
}

function NotificationsModal() {
    const {notifications, toggleOpen, isOpen, setIsOpen, isLoaded, markAllRead} = useContext(NotificationsContext);
    console.table(notifications);

    function modalBody() {
        if (!isLoaded) {
            return <div id="notifications-empty" className="text-center text-muted">
                {__('notifications.loading')}
                {/* TODO: Add translation string to laravel body */}
            </div>
        }

        if (notifications.length === 0)
            return <div id="notifications-empty" className="text-center text-muted">
                {__('notifications.empty')}
                <br/>¯\_(ツ)_/¯
            </div>;

        return <ul>
            {notifications.map(notification => <NotificationItem {...notification} />)}
        </ul>;
    }

    function onMarkAllReadClick() {
        fetch("/notifications/readAll", {
            method: "POST",
            headers: {"X-CSRF-TOKEN": token}
        })
            .then(markAllRead);
    }

    return <MDBModal show={isOpen} setShow={setIsOpen} tabIndex={-1}>
        <MDBModalDialog>
            <MDBModalContent>
                <MDBModalHeader>
                    <MDBModalTitle>{__('notifications.title')}</MDBModalTitle>
                    {/* TODO: Adjust so it looks correctly. */}
                    {/* TODO: Move to an MDBBtn. */}
                    <a href="javascript:void(0)"
                       className="text-muted"
                       onClick={onMarkAllReadClick}
                       aria-label={__('notifications.mark-all-read')}>
                        <span aria-hidden="true"><i className="fa-solid fa-check-double"></i></span>
                    </a>
                    <MDBBtn className='btn-close' color='none' onClick={toggleOpen}></MDBBtn>
                </MDBModalHeader>
                <MDBModalBody>
                    {modalBody()
                    }
                </MDBModalBody>
            </MDBModalContent>
        </MDBModalDialog>
    </MDBModal>;
}

// TODO: Place the correct data (will need API changes) and make pretty
function NotificationItem(notification) {
    const {toggleRead} = useContext(NotificationsContext);

    function onToggleReadButtonClick() {
        fetch("/notifications/toggleReadState/" + notification.id, { // TODO: Use the API instead
            method: "POST",
            headers: {"X-CSRF-TOKEN": window.token}
        })
            .then(() => {
                toggleRead(notification.id);
            });
    }

    return <li>
        {notification.notifiable_type} <MDBBtn onClick={onToggleReadButtonClick}>{isUnread(notification)
        ? __("notifications.mark-read")
        : __("notifications.mark-unread")}</MDBBtn>
    </li>;
}

render(<NotificationsContextProvider>
        <NotificationsNavButton/>
        <NotificationsModal/>
    </NotificationsContextProvider>,
    document.getElementById("notifications-button"));
