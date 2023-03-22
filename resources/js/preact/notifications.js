import {h, render} from 'preact';
import {useContext, useEffect, useState} from "preact/hooks";
import {createContext} from 'preact/compat';
import classNames from "classnames";

const NotificationsContext = createContext();

function NotificationsContextProvider({children}) {
    const [notifications, setNotifications] = useState([]);
    const [isOpen, setIsOpen]               = useState(false);
    const [isLoaded, setIsLoaded]           = useState(false);

    const toggleOpen = () => setIsOpen(open => !open);

    useEffect(() => {
        fetch("/notifications/latest") // TODO: Use the API instead, needs sufficient permissions on API routes
            .then(res => res.json())
            .then(setNotifications)
            .catch(e => console.error(e))
            .finally(() => setIsLoaded(true))

    }, []);

    return <NotificationsContext.Provider
        value={{notifications, isOpen, isLoaded, toggleOpen}}>
        {children}
    </NotificationsContext.Provider>
}

function NotificationsNavButton() {
    const {notifications, toggleOpen, isLoaded} = useContext(NotificationsContext);

    let numUnread = notifications.filter(n => n.read_at === null).length;

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

render(<NotificationsContextProvider><NotificationsNavButton/></NotificationsContextProvider>,
    document.getElementById("notifications-button"));

const __ = (s) => s;

function NotificationsModal() {
    const {notifications, toggleOpen, isOpen, isLoaded} = useContext(NotificationsContext);

    console.log(isOpen)

    return (<div className="modal fade bd-example-modal-lg" id="notifications-board" tabIndex="-1" role="dialog"
                 aria-hidden={isOpen ? "false" : "true"} aria-labelledby="notifications-board-title">
        <div className="modal-dialog modal-lg modal-dialog-scrollable">
            <div className="modal-content">
                <div className="modal-header">
                    <h2 className="modal-title fs-4" id="notifications-board-title">
                        {__('notifications.title')}
                    </h2>
                    <a href="javascript:void(0)" className="text-muted" id="mark-all-read"
                       aria-label={ __('notifications.mark-all-read') }>
                        <span aria-hidden="true"><i className="fa-solid fa-check-double"></i></span>
                    </a>
                    <button type="button" className="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div className="modal-body" id="notifications-list">
                    <div id="notifications-empty" className="text-center text-muted">
                        {__('notifications.empty')}
                        <br/>¯\_(ツ)_/¯
                    </div>
                </div>
            </div>
        </div>
    </div>);
}

render(<NotificationsContextProvider><NotificationsModal/></NotificationsContextProvider>,
    document.getElementById("notifications-modal"));
