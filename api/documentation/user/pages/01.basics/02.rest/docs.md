---
title: REST
taxonomy:
    category: docs
---

The REST architectural style was developed by W3C Technical Architecture Group (TAG) in parallel with HTTP 1.1, based on the existing design of HTTP 1.0. The World Wide Web represents the largest implementation of a system conforming to the REST architectural style.

REST-style architectures conventionally consist of clients and servers. Clients initiate requests to servers; servers process requests and return appropriate responses. Requests and responses are built around the transfer of representations of resources. A resource can be any body of information, static or variable. A representation of a resource is typically a document that captures the current or intended state of a resource.

The client begins sending requests when it is ready to make the transition to a new state. While one or more requests are outstanding, the client is considered to be in transition. The representation of each application state contains links that may be used the next time the client chooses to initiate a new state-transition.

## Design goals

*. Performance
*. Scalability of component interactions
*. Simplicity of interfaces
*. Modifiability of components to meet changing needs (even while the application is running)
*. Visibility of communication between components by service agents
*. Portability of component deployment
*. Reliability

REST has been applied to describe the desired web architecture, to help identify existing problems, to compare alternative solutions, and to ensure that protocol extensions would not violate the core constraints that make the Web successful.

## Request methods

*. GET (retrieve)
*. POST (create)
*. PUT (modify)
*. DELETE (delete)

The Flipit API uses these request methods for retrieving, creating, modifying and deleting store data. Check out [Wikipedia](https://en.wikipedia.org/wiki/Representational_state_transfer) for further information about REST.

## JSON

The Flipit API uses the JSON format to exchange data. Javascript Object Notation [JSON](https://en.wikipedia.org/wiki/JSON), emerged as a standard for easily exchanging Javscript object data between systems. Modern Javascript is designed to natively read that data and deserialize it into objects, making it available to the rest of the code running in the system. As computing power increased alongside improved network bandwidth, Javascript evolved into a mature and powerful language running entirely within the web browser. With Javascript as a client, many API producers began returning data in its native format, avoiding the need for black box XML code libraries and the bloat that often comes with them.

JSON’s simplicity has made it a favored data exchange format for several other agile languages as well. JSON is easier to parse than XML and its structure is much lighter.