#include <iostream>
#include <cstdlib>
#include <ctime>

int main() {
    std::cout << "Content-Type: text/html\n\n";

    // Basic request info
    const char* method = getenv("REQUEST_METHOD");
    const char* query = getenv("QUERY_STRING");
    const char* ip = getenv("REMOTE_ADDR");
    const char* agent = getenv("HTTP_USER_AGENT");

    std::time_t now = std::time(nullptr);

    std::cout << "<html><head><title>C++ Echo</title></head><body>";
    std::cout << "<h1>C++ Echo Response</h1>";

    std::cout << "<p><strong>Team Member:</strong> Sanila Silva</p>";
    std::cout << "<p><strong>Language:</strong> C++</p>";
    std::cout << "<p><strong>Time:</strong> " << std::ctime(&now) << "</p>";

    std::cout << "<hr>";

    std::cout << "<p><strong>Method:</strong> "
              << (method ? method : "UNKNOWN") << "</p>";

    std::cout << "<p><strong>IP Address:</strong> "
              << (ip ? ip : "UNKNOWN") << "</p>";

    std::cout << "<p><strong>User Agent:</strong> "
              << (agent ? agent : "UNKNOWN") << "</p>";

    std::cout << "<hr>";

    // Echo input
    if (method && std::string(method) == "GET") {
        std::cout << "<h3>GET Data</h3>";
        std::cout << "<pre>" << (query ? query : "") << "</pre>";
    }

    if (method && std::string(method) == "POST") {
        std::cout << "<h3>POST Data</h3>";
        std::string body;
        char ch;
        while (std::cin.get(ch)) {
            body += ch;
        }
        std::cout << "<pre>" << body << "</pre>";
    }

    std::cout << "<hr>";
    std::cout << "<a href=\"/cpp-echo-form.html\">Back to Echo Form</a>";
    std::cout << "</body></html>";

    return 0;
}
