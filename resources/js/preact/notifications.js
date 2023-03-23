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

        return notifications.map(notification => <NotificationItem {...notification} />);
    }

    function onMarkAllReadClick() {
        fetch("/notifications/readAll", {
            method: "POST",
            headers: {"X-CSRF-TOKEN": token}
        })
            .then(markAllRead);
    }

    return <MDBModal show={isOpen} setShow={setIsOpen} tabIndex={-1}>
        <MDBModalDialog id='notifications-board'>
            <MDBModalContent>
                <MDBModalHeader id='modal-header'>
                    <MDBModalTitle>{__('notifications.title')}</MDBModalTitle>
                    {/* TODO: Adjust so it looks correctly. */}
                    {/* TODO: Move to an MDBBtn. */}
                    <a href="javascript:void(0)"
                       className="text-muted"
                       onClick={onMarkAllReadClick}
                       aria-label={__('notifications.mark-all-read')}>
                        <span aria-hidden="true"><i className="fa-solid fa-check-double"></i></span>
                    </a>
                    <MDBBtn id='mark-all-read' className='btn-close' color='none' onClick={toggleOpen}></MDBBtn>
                </MDBModalHeader>
                <MDBModalBody id="notifications-list">
                    {modalBody()}
                </MDBModalBody>
            </MDBModalContent>
        </MDBModalDialog>
    </MDBModal>;
}

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

    let unread = isUnread(notification);

    return <div className={classNames("row", notification.color, {"unread": unread})}>
        <a className="col-1 col-sm-1 align-left lead" href={notification.link}>
            <i className={notification.icon}></i>
        </a>
        <a className="col-7 col-sm-8 align-middle" href={notification.link}>
            <p className="lead" dangerouslySetInnerHTML={{__html: notification.lead}}></p>
            <span dangerouslySetInnerHTML={{__html: notification.notice}}></span>
        </a>
        <div className="col col-sm-3 text-end">
            <button type="button" className="interact toggleReadState" onClick={onToggleReadButtonClick}>
                <span aria-hidden="true" aria-label={unread
                    ? __("notifications.mark-read")
                    : __("notifications.mark-unread")}>
                    <i className={classNames("far", {"fa-envelope": unread, "fa-envelope-open": !unread})}></i>
                </span>
            </button>
            <div className="text-muted">{notification.date_for_humans}</div>
        </div>
    </div>;
}

render(<NotificationsContextProvider>
        <NotificationsNavButton/>
        <NotificationsModal/>
    </NotificationsContextProvider>,
    document.getElementById("notifications-button"));
