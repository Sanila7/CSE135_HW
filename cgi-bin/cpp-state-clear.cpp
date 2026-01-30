#include <iostream>

int main() {
    std::cout << "Content-Type: text/html\n";
    std::cout << "Set-Cookie: favorite_color=; expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/\n\n";

    std::cout << "<html><body>";
    std::cout << "<h1>State Cleared</h1>";
    std::cout << "<a href=\"/cpp-state-form.html\">Back to Form</a>";
    std::cout << "</body></html>";

    return 0;
}
