#include <iostream>
#include <cstdlib>
#include <string>

int main() {
    std::cout << "Content-Type: text/html\n";

    // Read query string
    const char* query = getenv("QUERY_STRING");
    std::string color = "";

    if (query && std::string(query).find("color=") != std::string::npos) {
        color = std::string(query).substr(std::string(query).find("=") + 1);

        // Save state in cookie
        std::cout << "Set-Cookie: favorite_color=" << color << "; Path=/\n";
    }

    // Read cookie
    const char* cookie = getenv("HTTP_COOKIE");
    std::string savedColor = "None";

    if (cookie && std::string(cookie).find("favorite_color=") != std::string::npos) {
        std::string c(cookie);
        savedColor = c.substr(c.find("favorite_color=") + 15);
    }

    std::cout << "\n<html><body>";
    std::cout << "<h1>C++ Saved State</h1>";
    std::cout << "<p><strong>Favorite Color:</strong> " << savedColor << "</p>";

    std::cout << "<br>";
    std::cout << "<a href=\"/cpp-state-form.html\">Back to Form</a><br>";
    std::cout << "<a href=\"/cgi-bin/cpp-state-clear\">Clear State</a>";

    std::cout << "</body></html>";

    return 0;
}
