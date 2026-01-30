#include <iostream>
#include <cstdlib>

extern char **environ;

int main() {
    // CGI header
    std::cout << "Content-Type: text/html\n\n";

    std::cout << "<html><head><title>C++ Environment Variables</title></head>";
    std::cout << "<body>";
    std::cout << "<h1>C++ Environment Variables</h1>";
    std::cout << "<p><strong>Language:</strong> C++</p>";
    std::cout << "<hr>";
    std::cout << "<ul>";

    // Loop through environment variables
    for (char **env = environ; *env != nullptr; env++) {
        std::cout << "<li>" << *env << "</li>";
    }

    std::cout << "</ul>";
    std::cout << "</body></html>";

    return 0;
}
